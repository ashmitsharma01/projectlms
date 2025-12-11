<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccessCodeOlympiad;
use App\Models\D2CDigitalContent;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\MediaFiles;
use App\Models\MediaFolder;
use App\Models\MediaGallery;
use App\Models\OnlineClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolComplimentaryCourse;
use App\Models\StudentDetails;
use App\Models\Subject;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPurchase;
use App\Models\TrackUserVideoProgress;
use App\Models\TransactionLog;
use App\Models\User;
use App\Models\UserAdditionalDetail;
use App\Models\UserClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CoreController extends Controller
{
    public $data = [];
    public $res  = [];

    // Below are the functions related to user portals.
    public static function getUserDashboard($request)
    {
        //online classes week wise
        $res['today']        = Carbon::today();
        $today               = Carbon::today();
        $res['currentMonth'] = $today->format('F');
        if ($today->dayOfWeek === Carbon::SUNDAY) {
            $startOfWeek = $today->subDays(6);
        } else {
            $startOfWeek = $today->startOfWeek();
        }
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $res['datesInWeek'] = [];
        $period             = \Carbon\CarbonPeriod::create($startOfWeek, $endOfWeek);

        foreach ($period as $date) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $res['datesInWeek'][] = [
                    'date' => $date,
                    'day'  => $date->format('D'),
                ];
            }
        }

        //assigned teachers
        $class_id  = StudentDetails::where('user_id', Auth::id())->pluck('class')->first();
        $school_id = UserAdditionalDetail::where('user_id', Auth::id())->pluck('school_id')->first();
        $teachers  = UserAdditionalDetail::with('user')->where('school_id', $school_id)
            ->where('role', 'school_teacher')
            ->get();

        $res['matchingTeachers'] = [];
        foreach ($teachers as $index => $teacher) {
            $assignedClasses  = explode(',', $teacher->assigned_classes);
            $assignedSubjects = explode(',', $teacher->assigned_subjects);
            $subjectNames     = [];
            $subjects         = Subject::whereIn('id', $assignedSubjects)->get();
            foreach ($subjects as $subject) {
                $subjectNames[] = $subject->name;
            }
            $subjectNamesString = implode(', ', $subjectNames);
            if (in_array($class_id, $assignedClasses)) {
                $res['matchingTeachers'][$index] = [
                    'teacher'  => $teacher,
                    'subjects' => $subjectNamesString,
                ];
            }
        }
        return $res;
    }
    public static function getUserMyCourses($request)
    {
        $userBoard    = getUserSchoolBoard();
        $userMedium   = getUserSchoolMedium();
        $userSeries   = getUserSchoolSeries();
        $userClass    = Auth::user()->studentDetails->class ?? '';
        $userId       = Auth::id();
        $userGroup       = Auth::user()->category_id ?? '';
        $schoolId     = StudentDetails::where('user_id', $userId)->value('school_id');
        $schoolStatus = User::where('id', $schoolId)->value('status');
        $userClassesSchStudent = UserClass::where('user_id', $userId)->exists();
        $daysLeft     = '';
        // dd($schoolId, $userId, $userBoard, $userMedium, $userSeries, $userClass);
        if ($request->from == "web") {
            $res['academic_courses']    = collect();
            $res['nonacademic_courses'] = collect();
            $res['academic_activity_courses'] = collect();

            if ($userClass) {
                if (getUserRoles() === 'd2c_user') {
                    $userId = auth()->id();
                    $userClasses = UserClass::where('user_id', $userId)->get();

                    $academic_courses = collect();
                    $nonacademic_courses = collect();

                    foreach ($userClasses as $userClass) {
                        $classId = $userClass->class_id;
                        $groupId = $userClass->category_id;

                        if ($groupId == '35') {
                            $userAccessCodeAssignedCourses = AccessCodeOlympiad::where('user_id', $userId)->get();
                            $academic_courses = collect();
                            $addedReasoning = [];


                            foreach ($userAccessCodeAssignedCourses as $accessCode) {
                                if ($accessCode->expiration_date <= now()->format('Y-m-d')) {
                                    continue; // Skip this access code if it has expired
                                }

                                $seriesId = $accessCode->book_series_id;
                                $classIdAccessCode = $accessCode->class_id;
                                $subjectId = $accessCode->subject_id;

                                // Get the current subject's course
                                $courses = Course::where('category_id', 1)
                                    ->where('is_active', 1)
                                    ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                        $query->where('field_name', 'series')->where('field_value', $seriesId);
                                    })
                                    ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                        $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                                    })
                                    ->whereHas('metadataValues', function ($query) use ($subjectId) {
                                        $query->where('field_name', 'subject')->where('field_value', $subjectId);
                                    })
                                    ->with('metadataValues')
                                    ->get();

                                $academic_courses = $academic_courses->merge($courses);

                                // Add Reasoning only once per (class + series)
                                // Reasoning Subject Id is 60
                                $key = $classIdAccessCode . '-' . $seriesId;

                                if (!in_array($key, $addedReasoning)) {
                                    $reasoningCourses = Course::where('category_id', 1)
                                        ->where('is_active', 1)
                                        ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                            $query->where('field_name', 'series')->where('field_value', $seriesId);
                                        })
                                        ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                            $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                                        })
                                        ->whereHas('metadataValues', function ($query) {
                                            $query->where('field_name', 'subject')->where('field_value', '60');
                                        })
                                        ->with('metadataValues')
                                        ->get();

                                    $academic_courses = $academic_courses->merge($reasoningCourses);
                                    $addedReasoning[] = $key;
                                }
                            }
                        } else {
                            $userMediumId = $userClass->medium_id;

                            $assignedCourses = D2CDigitalContent::where('class_id', $classId)
                                ->when(!is_null($groupId), function ($query) use ($groupId) {
                                    $query->where(function ($q) use ($groupId) {
                                        $q->where('d2c_content_id', $groupId)
                                            ->orWhere(function ($inner) use ($groupId) {
                                                $inner->whereNull('d2c_content_id')
                                                    ->where('sub_category_id', $groupId);
                                            });
                                    });
                                })
                                ->get()
                                ->filter(function ($item) use ($userMediumId) {
                                    return $item->medium_id === null || $item->medium_id == $userMediumId;
                                });

                            foreach ($assignedCourses as $assignedCourse) {
                                $courseIds = explode(',', $assignedCourse->course_id);

                                if ($assignedCourse->category_id == 1) {
                                    $academic_courses = $academic_courses->merge(
                                        Course::where('category_id', 1)
                                            ->where('is_active', 1)
                                            ->whereIn('id', $courseIds)
                                            ->with('metadataValues')
                                            ->get()
                                    );
                                } elseif ($assignedCourse->category_id == 2) {
                                    $nonacademic_courses = $nonacademic_courses->merge(
                                        Course::where('category_id', 2)
                                            ->where('is_active', 1)
                                            ->whereIn('id', $courseIds)
                                            ->with('metadataValues')
                                            ->get()
                                    );
                                }
                            }
                        }
                    }

                    // Final result cleanup
                    $res['academic_courses'] = $academic_courses->unique('id')->values();
                    $res['nonacademic_courses'] = $nonacademic_courses->unique('id')->values();
                    $res['academic_activity_courses'] = collect();
                } elseif ($userClass && getUserRoles() == 'b2c_student') {
                    // dd('1');
                    $user = auth()->user();
                    $createdAt = $user->created_at;
                    $now = now();
                    $daysLeft = 15 - $now->diffInDays($createdAt);

                    // Complimentary (free) academic courses within 15 days
                    $academic_courses = collect();
                    $nonacademic_courses = collect();

                    if ($now->diffInDays($createdAt) <= 15) {
                        $academic_courses = Course::where('category_id', 1)
                            ->where('is_active', 1)
                            ->whereHas('metadataValues', function ($query) use ($userClass) {
                                $query->where('field_name', 'class')->where('field_value', $userClass);
                            })
                            ->whereHas('metadataValues', function ($query) {
                                $query->where('field_name', 'series')->where('field_value', 19);
                            })
                            ->with('metadataValues')
                            ->get();

                        $isComplimentaryPackage = 1;
                        $nonacademic_courses = Course::where('category_id', 2)
                            ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
                                $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
                            })
                            ->with('metadataValues')
                            ->where('is_active', 1)
                            ->get();
                    }

                    // Fetch subscribed courses regardless of time
                    $subscriptionPurchase = SubscriptionPurchase::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->get();

                    $subscribed_academic = collect();
                    $subscribed_nonacademic = collect();

                    foreach ($subscriptionPurchase as $purchase) {
                        $coursesData = json_decode($purchase->courses_json, true);
                        $academicCourseIds = array_column($coursesData['academic_courses'] ?? [], 'id');
                        $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

                        $subscribed_academic = $subscribed_academic->merge(
                            Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->with('metadataValues')->get()
                        );

                        $subscribed_nonacademic = $subscribed_nonacademic->merge(
                            Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->with('metadataValues')->get()
                        );
                    }

                    // Add Access Code content for B2C student (same as D2C)
                    $userAccessCodeAssignedCourses = AccessCodeOlympiad::where('user_id', $user->id)->get();
                    $addedReasoning = [];

                    foreach ($userAccessCodeAssignedCourses as $accessCode) {
                        if ($accessCode->expiration_date <= now()->format('Y-m-d')) {
                            continue; // Skip expired codes
                        }

                        $seriesId = $accessCode->book_series_id;
                        $classIdAccessCode = $accessCode->class_id;
                        $subjectId = $accessCode->subject_id;

                        $courses = Course::where('category_id', 1)
                            ->where('is_active', 1)
                            ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                $query->where('field_name', 'series')->where('field_value', $seriesId);
                            })
                            ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                            })
                            ->whereHas('metadataValues', function ($query) use ($subjectId) {
                                $query->where('field_name', 'subject')->where('field_value', $subjectId);
                            })
                            ->with('metadataValues')
                            ->get();

                        $academic_courses = $academic_courses->merge($courses);

                        // Add Reasoning only once per (class + series) — Subject 60
                        $key = $classIdAccessCode . '-' . $seriesId;
                        if (!in_array($key, $addedReasoning)) {
                            $reasoningCourses = Course::where('category_id', 1)
                                ->where('is_active', 1)
                                ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                    $query->where('field_name', 'series')->where('field_value', $seriesId);
                                })
                                ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                    $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                                })
                                ->whereHas('metadataValues', function ($query) {
                                    $query->where('field_name', 'subject')->where('field_value', '60');
                                })
                                ->with('metadataValues')
                                ->get();

                            $academic_courses = $academic_courses->merge($reasoningCourses);
                            $addedReasoning[] = $key;
                        }
                    }

                    // Merge complimentary + access code + subscribed and remove duplicates
                    $res['academic_courses'] = $academic_courses->merge($subscribed_academic)->unique('id')->values();
                    $res['nonacademic_courses'] = $nonacademic_courses->merge($subscribed_nonacademic)->unique('id')->values();
                    $res['days_left'] = max(0, $daysLeft);
                    $res['academic_activity_courses'] = collect();

                    return $res;
                } elseif ($schoolStatus == 1 && getUserRoles() == 'school_student' && $userClassesSchStudent) {
                    $userId = auth()->id();
                    $userClasses = UserClass::where('user_id', $userId)->get();

                    $academic_courses = collect();
                    $nonacademic_courses = collect();
                    $academic_activity_courses = collect();
                    $processedSubjects = [];

                    foreach ($userClasses as $userClass) {
                        $classId = $userClass->class_id;
                        $seriesId = $userClass->book_series_id;
                        $userSubjectId = $userClass->subject_id; // new subject from userClass

                        // Get all subject_ids assigned to the school for this class + series
                        $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $schoolId)
                            ->where('class_id', $classId)
                            ->where('series_id', $seriesId)
                            ->get();

                        $schoolSubjectIds = [];
                        foreach ($schoolAssignedDigitalContent as $digitalContent) {
                            $ids = explode(',', $digitalContent->subject_id);
                            $schoolSubjectIds = array_merge($schoolSubjectIds, $ids);
                        }
                        $schoolSubjectIds = array_unique(array_filter($schoolSubjectIds));

                        // Add the subject from userClass if not present in school's assigned subjects
                        if ($userSubjectId && !in_array($userSubjectId, $schoolSubjectIds)) {
                            $schoolSubjectIds[] = $userSubjectId;
                        }

                        // Now for each subject in the combined list, fetch courses
                        foreach ($schoolSubjectIds as $subjectId) {
                            $key = $classId . '_' . $seriesId;

                            if (!isset($processedSubjects[$key])) {
                                $processedSubjects[$key] = [];
                            }

                            if (!in_array($subjectId, $processedSubjects[$key])) {
                                $processedSubjects[$key][] = $subjectId;

                                $coursesQuery = Course::where('category_id', 1)
                                    ->where('is_active', 1)
                                    ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                        $query->where('field_name', 'series')->where('field_value', $seriesId);
                                    })
                                    ->whereHas('metadataValues', function ($query) use ($classId) {
                                        $query->where('field_name', 'class')->where('field_value', $classId);
                                    })
                                    ->whereHas('metadataValues', function ($query) use ($subjectId) {
                                        $query->where('field_name', 'subject')->where('field_value', $subjectId);
                                    });

                                $courses = $coursesQuery->with('metadataValues')->get();
                                $academic_courses = $academic_courses->merge($courses);
                            }
                        }
                    }

                    $isComplimentaryPackage = 1;
                    $nonacademic_courses = Course::where('category_id', 2)
                        ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
                            $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
                        })
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    // Fetch the ACADEMIC ACTIVITY (IF ANY) courses
                    $schoolAssignedActivity = SchoolComplimentaryCourse::where('school_id', $schoolId)->where('category_id', 1)
                        ->pluck('course_id')->toArray();

                    $academic_activity_courses = Course::where('category_id', 1)->where('sub_category_id', 37)->whereIn('id', $schoolAssignedActivity)
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    // Filter academic_activity_courses by user's class
                    $academic_activity_coursesQuery = Course::where('category_id', 1)->where('sub_category_id', 37)->whereIn('id', $schoolAssignedActivity)
                        ->where('is_active', 1);

                    $academic_activity_coursesQuery->whereHas('metadataValues', function ($query) use ($userClass) {
                        $query->where('field_name', 'class')->where('field_value', $userClass->class_id);
                    });

                    $academic_activity_courses = $academic_activity_coursesQuery->with('metadataValues')->get();

                    $res['academic_courses'] = $academic_courses;
                    $res['nonacademic_courses'] = $nonacademic_courses;
                    $res['academic_activity_courses'] = $academic_activity_courses;
                } elseif ($schoolStatus == 1 && getUserRoles() == 'school_student') {
                    // Fetch assigned digital content for the school
                    $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $schoolId)
                        ->where('class_id', $userClass)
                        ->orderBy('series_id', 'asc') // Ensure the first series gets priority
                        ->get();
                    // dd($schoolAssignedDigitalContent);

                    $academic_courses  = collect();
                    $processedSubjects = []; // To track unique subjects

                    foreach ($schoolAssignedDigitalContent as $digitalContent) {
                        $seriesId   = $digitalContent->series_id;
                        $subjectIds = explode(',', $digitalContent->subject_id);

                        foreach ($subjectIds as $subjectId) {
                            if (! in_array($subjectId, $processedSubjects)) {
                                // Add subject to processed list to prevent duplicates
                                $processedSubjects[] = $subjectId;

                                // Fetch courses only for unique subjects
                                $coursesQuery = Course::where('category_id', 1)
                                    ->where('is_active', 1);

                                // if (!empty($userBoard) && $userBoard != 0) {
                                //     $coursesQuery->whereHas('metadataValues', function ($query) use ($userBoard) {
                                //         $query->where('field_name', 'board')->where('field_value', $userBoard);
                                //     });
                                // }

                                // if (!empty($userMedium) && $userMedium != 0) {
                                //     $coursesQuery->whereHas('metadataValues', function ($query) use ($userMedium) {
                                //         $query->where('field_name', 'medium')->where('field_value', $userMedium);
                                //     });
                                // }



                                $coursesQuery->whereHas('metadataValues', function ($query) use ($seriesId) {
                                    $query->where('field_name', 'series')->where('field_value', $seriesId);
                                });

                                $coursesQuery->whereHas('metadataValues', function ($query) use ($userClass) {
                                    $query->where('field_name', 'class')->where('field_value', $userClass);
                                });

                                $coursesQuery->whereHas('metadataValues', function ($query) use ($subjectId) {
                                    $query->where('field_name', 'subject')->where('field_value', $subjectId);
                                });

                                $courses = $coursesQuery->with('metadataValues')->get();
                                $academic_courses = $academic_courses->merge($courses);
                            }
                        }
                    }

                    // Fetch the non-academic courses
                    $isComplimentaryPackage = 1;
                    $nonacademic_courses    = Course::where('category_id', 2)
                        ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
                            $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
                        })
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    // Fetch the ACADEMIC ACTIVIVITY(iF ANY) courses
                    $schoolAssignedActivity = SchoolComplimentaryCourse::where('school_id', $schoolId)->where('category_id', 1)
                        ->pluck('course_id')->toArray();

                    $academic_activity_courses    = Course::where('category_id', 1)->where('sub_category_id', 37)->whereIn('id', $schoolAssignedActivity)
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    $academic_activity_coursesQuery = Course::where('category_id', 1)->where('sub_category_id', 37)->whereIn('id', $schoolAssignedActivity)
                        ->where('is_active', 1);


                    $academic_activity_coursesQuery->whereHas('metadataValues', function ($query) use ($userClass) {
                        $query->where('field_name', 'class')->where('field_value', $userClass);
                    });

                    $academic_activity_courses = $academic_activity_coursesQuery->with('metadataValues')->get();


                    // ✅ Also fetch subscription courses if available
                    $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
                        ->where('status', 'active')
                        ->get();

                    $subscription_academic_courses    = collect();
                    $subscription_nonacademic_courses = collect();

                    foreach ($subscriptionPurchase as $purchase) {
                        $coursesData          = json_decode($purchase->courses_json, true);
                        $academicCourseIds    = array_column($coursesData['academic_courses'] ?? [], 'id');
                        $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

                        $subscription_academic_courses    = $subscription_academic_courses->merge(
                            Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->get()
                        );
                        $subscription_nonacademic_courses = $subscription_nonacademic_courses->merge(
                            Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->get()
                        );
                    }

                    // ✅ Merge school + subscription content
                    $res['academic_courses']           = $academic_courses->merge($subscription_academic_courses)->unique('id');
                    $res['nonacademic_courses']        = $nonacademic_courses->merge($subscription_nonacademic_courses)->unique('id');
                    $res['academic_activity_courses']  = $academic_activity_courses;
                } else {
                    // Fetch active subscriptions for the user
                    $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
                        ->where('status', 'active')
                        ->get();

                    $res = [];

                    foreach ($subscriptionPurchase as $purchase) {
                        $coursesData          = json_decode($purchase->courses_json, true);
                        $academicCourseIds    = array_column($coursesData['academic_courses'] ?? [], 'id');
                        $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

                        $res['academic_courses']    = Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->get();
                        $res['nonacademic_courses'] = Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->get();
                        $res['academic_activity_courses'] = collect();
                    }
                }
            } else {
                $user = auth()->user();
                $createdAt = $user->created_at;
                $now = now();

                $academic_courses = collect();
                $nonacademic_courses = collect();
                $addedReasoning = [];

                // Check if within 15-day complimentary window
                if ($now->diffInDays($createdAt) < 15) {
                    $daysLeft = 15 - $now->diffInDays($createdAt);

                    // Complimentary non-academic courses
                    $isComplimentaryPackage = 1;
                    $nonacademic_courses = Course::where('category_id', 2)
                        ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
                            $query->where('field_name', 'available_for_complimentary_package')
                                ->where('field_value', $isComplimentaryPackage);
                        })
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    $res['academic_courses'] = $academic_courses;
                    $res['nonacademic_courses'] = $nonacademic_courses;
                    $res['academic_activity_courses'] = collect();
                    $res['days_left'] = $daysLeft;
                }

                // Fetch Access Code courses for this user
                $userAccessCodeAssignedCourses = AccessCodeOlympiad::where('user_id', $user->id)->get();

                foreach ($userAccessCodeAssignedCourses as $accessCode) {
                    if ($accessCode->expiration_date <= now()->format('Y-m-d')) {
                        continue; // skip expired codes
                    }

                    $seriesId = $accessCode->book_series_id;
                    $classIdAccessCode = $accessCode->class_id;
                    $subjectId = $accessCode->subject_id;

                    $courses = Course::where('category_id', 1)
                        ->where('is_active', 1)
                        ->whereHas('metadataValues', function ($query) use ($seriesId) {
                            $query->where('field_name', 'series')->where('field_value', $seriesId);
                        })
                        ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                            $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                        })
                        ->whereHas('metadataValues', function ($query) use ($subjectId) {
                            $query->where('field_name', 'subject')->where('field_value', $subjectId);
                        })
                        ->with('metadataValues')
                        ->get();

                    $academic_courses = $academic_courses->merge($courses);

                    // Add Reasoning (subject 60) once per class+series
                    $key = $classIdAccessCode . '-' . $seriesId;
                    if (!in_array($key, $addedReasoning)) {
                        $reasoningCourses = Course::where('category_id', 1)
                            ->where('is_active', 1)
                            ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                $query->where('field_name', 'series')->where('field_value', $seriesId);
                            })
                            ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                            })
                            ->whereHas('metadataValues', function ($query) {
                                $query->where('field_name', 'subject')->where('field_value', '60');
                            })
                            ->with('metadataValues')
                            ->get();

                        $academic_courses = $academic_courses->merge($reasoningCourses);
                        $addedReasoning[] = $key;
                    }
                }

                // Fetch subscribed courses if any
                $subscriptionPurchase = SubscriptionPurchase::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->get();

                $subscribed_academic = collect();
                $subscribed_nonacademic = collect();

                foreach ($subscriptionPurchase as $purchase) {
                    $coursesData = json_decode($purchase->courses_json, true);
                    $academicCourseIds = array_column($coursesData['academic_courses'] ?? [], 'id');
                    $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

                    $subscribed_academic = $subscribed_academic->merge(
                        Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->with('metadataValues')->get()
                    );

                    $subscribed_nonacademic = $subscribed_nonacademic->merge(
                        Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->with('metadataValues')->get()
                    );
                }

                // Merge complimentary + access code + subscribed courses
                $res['academic_courses'] = $academic_courses->merge($subscribed_academic)->unique('id')->values();
                $res['nonacademic_courses'] = $nonacademic_courses->merge($subscribed_nonacademic)->unique('id')->values();
                $res['academic_activity_courses'] = collect();
                $res['days_left'] = max(0, $daysLeft ?? 0);
            }
        }

        if ($request->from == "app") {
            $res = ['academic_courses' => collect(), 'nonacademic_courses' => collect()]; // <-- ADD THIS

            if ($userClass) {

                if (getUserRoles() === 'd2c_user') {
                    $userId = auth()->id();
                    $userClasses = UserClass::where('user_id', $userId)->get();

                    $academic_courses = collect();
                    $nonacademic_courses = collect();

                    foreach ($userClasses as $userClass) {
                        $classId = $userClass->class_id;
                        $groupId = $userClass->category_id;

                        if ($groupId == '35') {
                            $userAccessCodeAssignedCourses = AccessCodeOlympiad::where('user_id', $userId)->get();
                            $academic_courses = collect();
                            $addedReasoning = [];


                            foreach ($userAccessCodeAssignedCourses as $accessCode) {
                                if ($accessCode->expiration_date <= now()->format('Y-m-d')) {
                                    continue; // Skip this access code if it has expired
                                }

                                $seriesId = $accessCode->book_series_id;
                                $classIdAccessCode = $accessCode->class_id;
                                $subjectId = $accessCode->subject_id;

                                // Get the current subject's course
                                $courses = Course::where('category_id', 1)
                                    ->where('is_active', 1)
                                    ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                        $query->where('field_name', 'series')->where('field_value', $seriesId);
                                    })
                                    ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                        $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                                    })
                                    ->whereHas('metadataValues', function ($query) use ($subjectId) {
                                        $query->where('field_name', 'subject')->where('field_value', $subjectId);
                                    })
                                    ->with('metadataValues')
                                    ->get();

                                $academic_courses = $academic_courses->merge($courses);

                                // Add Reasoning only once per (class + series)
                                // Reasoning Subject Id is 60
                                $key = $classIdAccessCode . '-' . $seriesId;

                                if (!in_array($key, $addedReasoning)) {
                                    $reasoningCourses = Course::where('category_id', 1)
                                        ->where('is_active', 1)
                                        ->whereHas('metadataValues', function ($query) use ($seriesId) {
                                            $query->where('field_name', 'series')->where('field_value', $seriesId);
                                        })
                                        ->whereHas('metadataValues', function ($query) use ($classIdAccessCode) {
                                            $query->where('field_name', 'class')->where('field_value', $classIdAccessCode);
                                        })
                                        ->whereHas('metadataValues', function ($query) {
                                            $query->where('field_name', 'subject')->where('field_value', '60');
                                        })
                                        ->with('metadataValues')
                                        ->get();

                                    $academic_courses = $academic_courses->merge($reasoningCourses);
                                    $addedReasoning[] = $key;
                                }
                            }
                        } else {
                            $userMediumId = $userClass->medium_id;

                            $assignedCourses = D2CDigitalContent::where('class_id', $classId)
                                ->when(!is_null($groupId), function ($query) use ($groupId) {
                                    $query->where(function ($q) use ($groupId) {
                                        $q->where('d2c_content_id', $groupId)
                                            ->orWhere(function ($inner) use ($groupId) {
                                                $inner->whereNull('d2c_content_id')
                                                    ->where('sub_category_id', $groupId);
                                            });
                                    });
                                })
                                ->get()
                                ->filter(function ($item) use ($userMediumId) {
                                    return $item->medium_id === null || $item->medium_id == $userMediumId;
                                });

                            foreach ($assignedCourses as $assignedCourse) {
                                $courseIds = explode(',', $assignedCourse->course_id);

                                if ($assignedCourse->category_id == 1) {
                                    $academic_courses = $academic_courses->merge(
                                        Course::where('category_id', 1)
                                            ->where('is_active', 1)
                                            ->whereIn('id', $courseIds)
                                            ->with('metadataValues')
                                            ->get()
                                    );
                                } elseif ($assignedCourse->category_id == 2) {
                                    $nonacademic_courses = $nonacademic_courses->merge(
                                        Course::where('category_id', 2)
                                            ->where('is_active', 1)
                                            ->whereIn('id', $courseIds)
                                            ->with('metadataValues')
                                            ->get()
                                    );
                                }
                            }
                        }
                    }

                    // Final result cleanup
                    $res['academic_courses'] = $academic_courses->unique('id')->values();
                    $res['nonacademic_courses'] = $nonacademic_courses->unique('id')->values();
                    $res['academic_activity_courses'] = collect();
                } elseif ($userClass && getUserRoles() == 'b2c_student') {
                    $user = auth()->user();
                    $createdAt = $user->created_at;
                    $now = now();
                    $daysLeft = 15 - $now->diffInDays($createdAt);

                    // Complimentary (free) academic courses within 15 days
                    $academic_courses = collect();
                    $nonacademic_courses = collect();

                    if ($now->diffInDays($createdAt) <= 15) {
                        $academic_courses = Course::where('category_id', 1)
                            ->where('is_active', 1)
                            ->whereHas('metadataValues', function ($query) use ($userClass) {
                                $query->where('field_name', 'class')->where('field_value', $userClass);
                            })
                            ->whereHas('metadataValues', function ($query) {
                                $query->where('field_name', 'series')->where('field_value', 19);
                            })
                            ->with('metadataValues')
                            ->get();

                        $isComplimentaryPackage = 1;
                        $nonacademic_courses = Course::where('category_id', 2)
                            ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
                                $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
                            })
                            ->with('metadataValues')
                            ->where('is_active', 1)
                            ->get();
                    }

                    // Fetch subscribed courses regardless of time
                    $subscriptionPurchase = SubscriptionPurchase::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->get();

                    $subscribed_academic = collect();
                    $subscribed_nonacademic = collect();

                    foreach ($subscriptionPurchase as $purchase) {
                        $coursesData = json_decode($purchase->courses_json, true);
                        $academicCourseIds = array_column($coursesData['academic_courses'] ?? [], 'id');
                        $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

                        $subscribed_academic = $subscribed_academic->merge(
                            Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->with('metadataValues')->get()
                        );

                        $subscribed_nonacademic = $subscribed_nonacademic->merge(
                            Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->with('metadataValues')->get()
                        );
                    }

                    // Merge complimentary + subscribed and remove duplicates (by ID)
                    $res['academic_courses'] = $academic_courses->merge($subscribed_academic)->unique('id')->values();
                    $res['nonacademic_courses'] = $nonacademic_courses->merge($subscribed_nonacademic)->unique('id')->values();
                    $res['days_left'] = max(0, $daysLeft);

                    return $res;
                } elseif ($schoolStatus == 1 && getUserRoles() == 'school_student') {
                    // Fetch assigned digital content for the school
                    $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $schoolId)
                        ->where('class_id', $userClass)
                        ->orderBy('series_id', 'asc') // Ensure the first series gets priority
                        ->get();
                    // dd($schoolAssignedDigitalContent);

                    $academic_courses  = collect();
                    $processedSubjects = []; // To track unique subjects

                    foreach ($schoolAssignedDigitalContent as $digitalContent) {
                        $seriesId   = $digitalContent->series_id;
                        $subjectIds = explode(',', $digitalContent->subject_id);

                        foreach ($subjectIds as $subjectId) {
                            if (! in_array($subjectId, $processedSubjects)) {
                                // Add subject to processed list to prevent duplicates
                                $processedSubjects[] = $subjectId;

                                // Fetch courses only for unique subjects
                                $coursesQuery = Course::where('category_id', 1)
                                    ->where('is_active', 1);

                                // if (!empty($userBoard) && $userBoard != 0) {
                                //     $coursesQuery->whereHas('metadataValues', function ($query) use ($userBoard) {
                                //         $query->where('field_name', 'board')->where('field_value', $userBoard);
                                //     });
                                // }

                                // if (!empty($userMedium) && $userMedium != 0) {
                                //     $coursesQuery->whereHas('metadataValues', function ($query) use ($userMedium) {
                                //         $query->where('field_name', 'medium')->where('field_value', $userMedium);
                                //     });
                                // }

                                $coursesQuery->whereHas('metadataValues', function ($query) use ($seriesId) {
                                    $query->where('field_name', 'series')->where('field_value', $seriesId);
                                });

                                $coursesQuery->whereHas('metadataValues', function ($query) use ($userClass) {
                                    $query->where('field_name', 'class')->where('field_value', $userClass);
                                });

                                $coursesQuery->whereHas('metadataValues', function ($query) use ($subjectId) {
                                    $query->where('field_name', 'subject')->where('field_value', $subjectId);
                                });

                                $courses = $coursesQuery->with('metadataValues')->get();

                                $academic_courses = $academic_courses->merge($courses);
                            }
                        }
                    }


                    // Fetch the non-academic courses
                    $isComplimentaryPackage = 1;
                    $nonacademic_courses    = Course::where('category_id', 2)
                        ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
                            $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
                        })
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    $res['academic_courses']    = $academic_courses;
                    $res['nonacademic_courses'] = $nonacademic_courses;
                } else {

                    // Fetch active subscriptions for the user
                    $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
                        ->where('status', 'active')
                        ->get();

                    $res = [];

                    foreach ($subscriptionPurchase as $purchase) {
                        $coursesData          = json_decode($purchase->courses_json, true);
                        $academicCourseIds    = array_column($coursesData['academic_courses'] ?? [], 'id');
                        $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

                        $res['academic_courses']    = Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->get();
                        $res['nonacademic_courses'] = Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->get();
                    }
                }
            } else {
                $user = auth()->user();
                $createdAt = $user->created_at;
                $now = now();

                // Check if within 15-day window (Day 0 to 14)
                if ($now->diffInDays($createdAt) < 15) {
                    $daysLeft = 15 - $now->diffInDays($createdAt);

                    $isComplimentaryPackage = 1;
                    $nonacademic_courses = Course::where('category_id', 2)
                        ->with('metadataValues')
                        ->where('is_active', 1)
                        ->get();

                    $res['academic_courses']    = collect();
                    $res['nonacademic_courses'] = $nonacademic_courses;
                    $res['days_left']           = $daysLeft;
                } else {
                    $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
                        ->where('status', 'active')
                        ->get();
                    $res = [];
                    foreach ($subscriptionPurchase as $purchase) {
                        $coursesData                = json_decode($purchase->courses_json, true);
                        $academicCourseIds          = array_column($coursesData['academic_courses'] ?? [], 'id');
                        $nonAcademicCourseIds       = array_column($coursesData['non_academic_courses'] ?? [], 'id');
                        $res['academic_courses']    = Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->get();
                        $res['nonacademic_courses'] = Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->get();
                    }
                }
            }
            // // old code before same as web 12 june 2025
            // if ($userClass) {
            //     if (getUserRoles() === 'd2c_user') {
            //         $userId = auth()->id();

            //         // Get all user class mappings (can be multiple)
            //         $userClasses = UserClass::where('user_id', $userId)->get();

            //         $academic_courses = collect();
            //         $nonacademic_courses = collect();

            //         foreach ($userClasses as $userClass) {
            //             $classId = $userClass->class_id;
            //             $groupId = $userClass->category_id; // This is what you called $userGroup before
            //             $userMediumId = $userClass->medium_id;
            //             // dd($groupId);
            //             // Get assigned D2C digital content for this class/group
            //             $assignedCourses = D2CDigitalContent::where('class_id', $classId)
            //                 ->when(!is_null($groupId), function ($query) use ($groupId) {
            //                     $query->where(function ($q) use ($groupId) {
            //                         $q->where('d2c_content_id', $groupId)
            //                             ->orWhere(function ($inner) use ($groupId) {
            //                                 $inner->whereNull('d2c_content_id')
            //                                     ->where('sub_category_id', $groupId);
            //                             });
            //                     });
            //                 })
            //                 ->get()
            //                 ->filter(function ($item) use ($userMediumId) {
            //                     // Only match if medium_id is present in D2C and matches user’s medium
            //                     return $item->medium_id === null || $item->medium_id == $userMediumId;
            //                 });

            //             foreach ($assignedCourses as $assignedCourse) {
            //                 $courseIds = explode(',', $assignedCourse->course_id);

            //                 if ($assignedCourse->category_id == 1) {
            //                     // Academic
            //                     $academic_courses = $academic_courses->merge(
            //                         Course::where('category_id', 1)
            //                             ->where('is_active', 1)
            //                             ->whereIn('id', $courseIds)
            //                             ->with('metadataValues')
            //                             ->get()
            //                     );
            //                 } elseif ($assignedCourse->category_id == 2) {
            //                     // Non-academic
            //                     $nonacademic_courses = $nonacademic_courses->merge(
            //                         Course::where('category_id', 2)
            //                             ->where('is_active', 1)
            //                             ->whereIn('id', $courseIds)
            //                             ->with('metadataValues')
            //                             ->get()
            //                     );
            //                 }
            //             }
            //         }

            //         // Remove duplicates by course ID
            //         $res['academic_courses'] = $academic_courses->unique('id')->values();
            //         $res['nonacademic_courses'] = $nonacademic_courses->unique('id')->values();
            //     } elseif ($userClass && getUserRoles() == 'b2c_student') {
            //         $user = auth()->user();
            //         $createdAt = $user->created_at;
            //         $now = now();
            //         // Check if within 15-day window
            //         if ($now->diffInDays($createdAt) <= 15) {
            //             $academic_courses = Course::where('category_id', 1)
            //                 ->where('is_active', 1)
            //                 ->whereHas('metadataValues', function ($query) use ($userClass) {
            //                     $query->where('field_name', 'class')->where('field_value', $userClass);
            //                 })
            //                 ->whereHas('metadataValues', function ($query) {
            //                     $query->where('field_name', 'series')->where('field_value', 19);
            //                 })
            //                 ->with('metadataValues')
            //                 ->get();
            //             // Fetch the non-academic courses
            //             $isComplimentaryPackage = 1;
            //             $nonacademic_courses    = Course::where('category_id', 2)
            //                 ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
            //                     $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
            //                 })
            //                 ->with('metadataValues')
            //                 ->where('is_active', 1)
            //                 ->get();

            //             $res['academic_courses']    = $academic_courses;
            //             $res['nonacademic_courses'] = $nonacademic_courses;
            //         } else {
            //             // After 15 days, hide courses
            //             $res['academic_courses'] = collect();
            //             $res['nonacademic_courses'] = collect();
            //         }

            //         return $res;
            //     } elseif ($schoolStatus == 1 && getUserRoles() == 'school_student') {
            //         // Fetch assigned digital content for the school
            //         $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $schoolId)
            //             ->where('class_id', $userClass)
            //             ->orderBy('series_id', 'asc') // Ensure the first series gets priority
            //             ->get();
            //         // dd($schoolAssignedDigitalContent);

            //         $academic_courses  = collect();
            //         $processedSubjects = []; // To track unique subjects

            //         foreach ($schoolAssignedDigitalContent as $digitalContent) {
            //             $seriesId   = $digitalContent->series_id;
            //             $subjectIds = explode(',', $digitalContent->subject_id);

            //             foreach ($subjectIds as $subjectId) {
            //                 if (! in_array($subjectId, $processedSubjects)) {
            //                     // Add subject to processed list to prevent duplicates
            //                     $processedSubjects[] = $subjectId;

            //                     // Fetch courses only for unique subjects
            //                     $coursesQuery = Course::where('category_id', 1)
            //                         ->where('is_active', 1);

            //                     if (!empty($userBoard) && $userBoard != 0) {
            //                         $coursesQuery->whereHas('metadataValues', function ($query) use ($userBoard) {
            //                             $query->where('field_name', 'board')->where('field_value', $userBoard);
            //                         });
            //                     }

            //                     if (!empty($userMedium) && $userMedium != 0) {
            //                         $coursesQuery->whereHas('metadataValues', function ($query) use ($userMedium) {
            //                             $query->where('field_name', 'medium')->where('field_value', $userMedium);
            //                         });
            //                     }

            //                     $coursesQuery->whereHas('metadataValues', function ($query) use ($seriesId) {
            //                         $query->where('field_name', 'series')->where('field_value', $seriesId);
            //                     });

            //                     $coursesQuery->whereHas('metadataValues', function ($query) use ($userClass) {
            //                         $query->where('field_name', 'class')->where('field_value', $userClass);
            //                     });

            //                     $coursesQuery->whereHas('metadataValues', function ($query) use ($subjectId) {
            //                         $query->where('field_name', 'subject')->where('field_value', $subjectId);
            //                     });

            //                     $courses = $coursesQuery->with('metadataValues')->get();

            //                     $academic_courses = $academic_courses->merge($courses);
            //                 }
            //             }
            //         }

            //         // Fetch the non-academic courses
            //         $isComplimentaryPackage = 1;
            //         $nonacademic_courses    = Course::where('category_id', 2)
            //             ->whereHas('metadataValues', function ($query) use ($isComplimentaryPackage) {
            //                 $query->where('field_name', 'available_for_complimentary_package')->where('field_value', $isComplimentaryPackage);
            //             })
            //             ->with('metadataValues')
            //             ->where('is_active', 1)
            //             ->get();

            //         $res['academic_courses']    = $academic_courses;
            //         $res['nonacademic_courses'] = $nonacademic_courses;
            //     } else {

            //         // Fetch active subscriptions for the user
            //         $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
            //             ->where('status', 'active')
            //             ->get();

            //         $res = [];

            //         foreach ($subscriptionPurchase as $purchase) {
            //             $coursesData          = json_decode($purchase->courses_json, true);
            //             $academicCourseIds    = array_column($coursesData['academic_courses'] ?? [], 'id');
            //             $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');

            //             $res['academic_courses']    = Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->get();
            //             $res['nonacademic_courses'] = Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->get();
            //         }
            //     }
            // } else {
            //     $user = auth()->user();
            //     $createdAt = $user->created_at;
            //     $now = now();

            //     // Check if within 15-day window (Day 0 to 14)
            //     if ($now->diffInDays($createdAt) < 15) {
            //         $daysLeft = 15 - $now->diffInDays($createdAt);

            //         $isComplimentaryPackage = 1;
            //         $nonacademic_courses = Course::where('category_id', 2)
            //             ->with('metadataValues')
            //             ->where('is_active', 1)
            //             ->get();

            //         $res['academic_courses']    = collect();
            //         $res['nonacademic_courses'] = $nonacademic_courses;
            //         $res['days_left']           = $daysLeft;
            //         // dd($daysLeft);
            //     } else {
            //         $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
            //             ->where('status', 'active')
            //             ->get();
            //         $res = [];
            //         foreach ($subscriptionPurchase as $purchase) {
            //             $coursesData                = json_decode($purchase->courses_json, true);
            //             $academicCourseIds          = array_column($coursesData['academic_courses'] ?? [], 'id');
            //             $nonAcademicCourseIds       = array_column($coursesData['non_academic_courses'] ?? [], 'id');
            //             $res['academic_courses']    = Course::whereIn('id', $academicCourseIds)->where('is_active', 1)->get();
            //             $res['nonacademic_courses'] = Course::whereIn('id', $nonAcademicCourseIds)->where('is_active', 1)->get();
            //         }
            //     }
            // }
        }

        return $res;
    }

    public static function getUserMyCoursesListing($request, $slug)
    {
        // if ($request->from == "web") {
        $res['courses'] = Course::where('slug', $slug)->with('metadataValues', 'totalChapters')->where('is_active', 1)->get();
        // }
        return $res;
    }
    public static function getUserMyCoursesContinueWatching($request)
    {
        $userId = Auth::id();

        if ($request->from == "web") {
            $res['courses'] = TrackUserVideoProgress::where('user_id', $userId)
                ->with(['course', 'chapter'])
                ->select('course_id')
                ->distinct()
                ->get();
            return $res;
        }
        if ($request->from == "app") {
            $user = Auth::user();
            $userId = $user->id;
            $userRole = getUserRoles(); // Make sure this function returns 'b2c_student' for B2C users

            $courses = TrackUserVideoProgress::where('user_id', $userId)
                ->with(['course', 'chapter'])
                ->select('course_id')
                ->distinct()
                ->get();

            $res['courses'] = [];

            // Preload subscription course IDs for b2c_student
            $subscribedCourseIds = [];
            if ($userRole === 'b2c_student') {
                $subscriptionPurchase = SubscriptionPurchase::where('user_id', $userId)
                    ->where('status', 'active')
                    ->get();

                foreach ($subscriptionPurchase as $purchase) {
                    $coursesData = json_decode($purchase->courses_json, true);
                    $academicCourseIds = array_column($coursesData['academic_courses'] ?? [], 'id');
                    $nonAcademicCourseIds = array_column($coursesData['non_academic_courses'] ?? [], 'id');
                    $subscribedCourseIds = array_merge($subscribedCourseIds, $academicCourseIds, $nonAcademicCourseIds);
                }

                $subscribedCourseIds = array_unique($subscribedCourseIds);
            }

            // Loop through each course and apply access control
            foreach ($courses as $course) {
                if ($course->course) {
                    $allowAccess = true;

                    if ($userRole === 'b2c_student') {
                        $allowAccess = false;

                        // 15-day trial check
                        $trialStartDate = $user->created_at;
                        $trialValidTill = $trialStartDate->copy()->addDays(15);
                        $isTrialValid = now()->lessThan($trialValidTill);

                        // Subscription check using preloaded list
                        $hasSubscription = in_array($course->course_id, $subscribedCourseIds);

                        if ($isTrialValid || $hasSubscription) {
                            $allowAccess = true;
                        }
                    }

                    if ($allowAccess) {
                        $userProgress = TrackUserVideoProgress::where('user_id', $userId)
                            ->where('course_id', $course->course_id)
                            ->get();

                        $videoDuration   = $userProgress->sum('video_duration');
                        $watchedDuration = $userProgress->sum('watched_duration');
                        $percentage      = $videoDuration > 0 ? ($watchedDuration / $videoDuration) * 100 : 0;

                        $totalChapters = CourseChapter::where('course_id', $course->course_id)->count();
                        $completedChapters = TrackUserVideoProgress::where('user_id', $userId)
                            ->where('course_id', $course->course_id)
                            ->whereColumn('watched_duration', '=', 'video_duration')
                            ->select('chapter_id')
                            ->distinct()
                            ->count();

                        $Image1 = $course->course->metadataValues->where('field_name', 'thumbnail_image')->value('field_value');
                        $Image2 = $course->course->metadataValues->where('field_name', 'banner_image')->value('field_value');
                        $Image3 = $course->course->metadataValues->where('field_name', 'book_cover_image')->value('field_value');

                        $courseData = [
                            'course_id'            => $course->course_id,
                            'course_name'          => $course->course->course_name,
                            'course_slug'          => $course->course->slug,
                            'course_image'         => $Image1 ? asset('storage/' . $Image1) : ($Image2 ? asset('storage/' . $Image2) : ($Image3 ? asset('storage/' . $Image3) : null)),
                            'percentage_completed' => round($percentage, 2),
                            'total_chapters'       => $totalChapters,
                            'completed_chapters'   => $completedChapters,
                        ];

                        $res['courses'][] = $courseData;
                    }
                }
            }

            return $res;
        }
    }

    public static function getUserMyCoursesChapterListing($request, $id)
    {
        $user = auth()->user();

        $res['course']         = Course::where('id', $id)->with('metadataValues')->where('is_active', 1)->first();
        $res['coursesChapter'] = CourseChapter::where('course_id', $id)
            ->with(['chapters' => function ($q) {
                $q->where('type', 'course_chapter'); // or whatever types you want
            }])
            ->get();

        // Default to false
        $res['certificateAvailable'] = false;
        $res['certificateDate'] = null;

        // Check if course has a metadata value for certification
        $res['hasCertificateMeta'] = $res['course']->metadataValues
            ->where('field_name', 'certification')
            ->where('field_value', '1')
            ->count() > 0;

        if ($res['hasCertificateMeta']) {
            // Get user subscription that contains this course
            $res['subscription'] = SubscriptionPurchase::where('user_id', $user->id)
                ->get()
                ->filter(function ($sub) use ($id) {
                    $courses = json_decode($sub->courses_json, true);
                    return is_array($courses) && in_array($id, $courses);
                })
                ->first();

            if ($res['subscription']) {
                $startDate = Carbon::parse($res['subscription']->start_date);
                $eligibleDate = $startDate->copy()->addDays(14);

                if (now()->greaterThanOrEqualTo($eligibleDate)) {
                    $res['certificateAvailable'] = true;
                    $res['certificateDate'] = today();
                }
            }
        }

        return $res;
    }


    public static function getCourseDigitalContent($request, $id)
    {
        // if ($request->from == "web") {
        $res['courseSlug'] = Course::where('id', $id)->value('slug');
        $res['chapters']   = CourseChapter::with('chapterListing', 'activityListing', 'folder', 'documents')->where('course_id', $id)->orderBy('sort_order', 'asc')->get();
        // }
        return $res;
    }
    public static function getUserOnlineClass($request)
    {
        // if ($request->from == "web") {
        $user                         = StudentDetails::where('user_id', Auth::id())->first();
        $res['ongoingOnlineClasses']  = OnlineClass::with('instructor', 'subject', 'class')->where('parent_id', $user->school_id)->where('class_id', $user->class)->where('status', 'ongoing')->get();
        $res['upcomingOnlineClasses'] = OnlineClass::with('instructor', 'subject', 'class')->where('parent_id', $user->school_id)->where('class_id', $user->class)->where('status', 'upcoming')->get();
        $res['pastOnlineClasses']     = OnlineClass::with('instructor', 'subject', 'class')->where('parent_id', $user->school_id)->where('class_id', $user->class)->where('status', 'past')->get();
        // }
        return $res;
    }
    public static function getUserOnlineClassContent($request, $id)
    {
        // if ($request->from == "web") {
        $res['content'] = MediaFiles::where('tbl_id', $id)->where('type', 'online_class_study_material')->get();
        // }
        return $res;
    }
    public static function getdigitalContent($request)
    {
        // if ($request->from == "web") {
        $schoolId     = Auth::user()->studentDetails->parent_id;
        $userClassId  = Auth::user()->studentDetails->class;



        // $userId       = Auth::id();
        // $parentId     = StudentDetails::where('user_id', $userId)->value('school_id');

        // $roleSlug = getUserRoles();

        // // Get school-assigned series
        // $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $parentId)
        //     ->whereNotNull('series_id')
        //     ->pluck('series_id')
        //     ->unique()
        //     ->toArray();

        // // Fetch folders based on series and role
        // $res['mittlearnFolderListing'] = MediaFolder::where('is_mittlearn_folder', 1)
        //     ->where(function ($query) use ($schoolAssignedSeries) {
        //         foreach ($schoolAssignedSeries as $seriesId) {
        //             $query->orWhereRaw("FIND_IN_SET(?, distribute_series_ids)", [$seriesId]);
        //         }
        //     })
        //     ->where(function ($query) use ($roleSlug) {
        //         $query->whereRaw("FIND_IN_SET(?, distribute_role_slug)", [$roleSlug]);
        //     })
        //     ->withCount('fileCount')
        //     ->get();
        // dd($res['mittlearnFolderListing']);





        $res['schoolContent']  = MediaFolder::where('parent_id', $schoolId)->where('class_id', null)->whereIn('available_to_users', ['students', 'all'])->withCount('fileCount')->get();
        $teacherId             = UserAdditionalDetail::where('role', 'school_teacher')->where('school_id', $schoolId)->get();
        $res['teacherContent'] = MediaFolder::where('parent_id', $schoolId)->where('class_id', $userClassId)->whereIn('available_to_users', ['students', 'all'])->withCount('fileCount')->get();
        // }
        return $res;
    }
    public static function getdigitalContentFiles($request, $id)
    {
        // if ($request->from == "web") {
        $res['folderId'] = MediaFolder::find($id);
        $res['files']    = MediaFiles::where('tbl_id', $id)->where('type', 'content_upload')->get();
        // }
        return $res;
    }
    public static function getmediaGallery($request)
    {
        // if ($request->from == "web") {
        $schoolId              = Auth::user()->studentDetails->parent_id;
        $res['schoolContent']  = MediaGallery::where('parent_id', $schoolId)->whereIn('available_to_users', ['all', 'students'])->get();
        // }
        return $res;
    }
    public static function getmediaGalleryFiles($request, $id)
    {
        // if ($request->from == "web") {
        $res['folderId'] = MediaGallery::find($id);
        $res['files']    = MediaFiles::where('tbl_id', $id)->where('type', 'school_media_gallery')->get();
        // }
        return $res;
    }
    public static function getUserSubscription($request)
    {
        // if ($request->from == "web") {
        $userId      = Auth::id();
        $res['plan'] = SubscriptionPurchase::with([
            'planDetails',
            'planPrice',
            'planFeatures',
            'transaction' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            },
        ])->where('user_id', $userId)->orderBy('created_at', 'desc')->first();
        if ($res['plan']) {
            $res['transactionLogsLatest'] = TransactionLog::with(['planDetails', 'planPrice'])
                ->where('user_id', $userId)
                ->latest()
                ->first();

            $res['transactionLogs'] = TransactionLog::with(['planDetails', 'planPrice'])->where('user_id', $userId)
                ->latest()
                ->skip(1)
                ->take(PHP_INT_MAX) // Ensures all remaining records are fetched
                ->get();
            $res['upgradedPlan'] = SubscriptionPlan::where('status', 1)->where('id', '>', $res['plan']->plan_id)->value('id');
        }
        $planId = $res['plan'] ? optional($res['plan']->planDetails)->id : 1;

        if (isset($res['plan']) && $res['plan']->planDetails->is_recommended === 1) {
            $res['recomendedPlan'] = SubscriptionPlan::where('status', 1)->with('subscriptionPlanFeature')->where('id', '>', $planId)->first();
        } else {
            $res['recomendedPlan'] = SubscriptionPlan::where('status', 1)->with('subscriptionPlanFeature')->where('is_recommended', 1)->first();
        }
        $res['user'] = User::with('studentDetails')->find($userId);
        // }
        return $res;
    }
    public static function getdigitalContentWithFiles()
    {
        $schoolId              = Auth::user()->studentDetails->parent_id;
        $userClassId           = Auth::user()->studentDetails->class;
        $res['schoolContent']  = MediaFolder::with('mediaFolderFiles')->where('parent_id', $schoolId)->where('class_id', null)->whereIn('available_to_users', ['students', 'all'])->withCount('fileCount')->get();
        $teacherId             = UserAdditionalDetail::where('role', 'school_teacher')->where('school_id', $schoolId)->get();
        $res['teacherContent'] = MediaFolder::with('mediaFolderFiles')->where('parent_id', $schoolId)->where('class_id', $userClassId)->whereIn('available_to_users', ['students', 'all'])->withCount('fileCount')->get();
        return $res;
    }
    public static function getMediaGalleryWithFiles()
    {
        $schoolId              = Auth::user()->studentDetails->parent_id;
        $res['schoolContent']  = MediaGallery::with('mediaGalleryFiles')->where('parent_id', $schoolId)->whereIn('available_to_users', ['all', 'students'])->get();
        return $res;
    }

    public static function storeStudentOverviewSection($request)
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $request->merge(['from' => 'web']);
        $courses = self::getUserMyCourses($request);
        if (! empty($courses)) {
            $totalAcadCourses    = $courses['academic_courses']->count();
            $totalNonAcadCourses = $courses['nonacademic_courses']->count();
        } else {
            $totalAcadCourses    = 0;
            $totalNonAcadCourses = 0;
        }

        $completedAcadCourses    = 0;
        $completedNonAcadCourses = 0;

        if (! empty($courses)) {
            foreach ($courses['academic_courses'] as $course) {
                $totalVideoDuration = TrackUserVideoProgress::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->sum('video_duration');
                $totalWatchedDuration = TrackUserVideoProgress::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->sum('watched_duration');
                if ($totalVideoDuration > 0 && $totalVideoDuration == $totalWatchedDuration) {
                    $completedAcadCourses++;
                }
            }
            foreach ($courses['nonacademic_courses'] as $course) {
                $totalVideoDuration = TrackUserVideoProgress::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->sum('video_duration');
                $totalWatchedDuration = TrackUserVideoProgress::where('user_id', Auth::id())
                    ->where('course_id', $course->id)
                    ->sum('watched_duration');
                if ($totalVideoDuration > 0 && $totalVideoDuration == $totalWatchedDuration) {
                    $completedNonAcadCourses++;
                }
            }
        }
        $acadCompletionPercentage = ($totalAcadCourses > 0)
            ? ($completedAcadCourses / $totalAcadCourses) * 100
            : 0;

        $nonAcadCompletionPercentage = ($totalNonAcadCourses > 0)
            ? ($completedNonAcadCourses / $totalNonAcadCourses) * 100
            : 0;

        $subscribedCourses = SubscriptionPurchase::where('user_id', Auth::id())->where('status', 'active')->first();
        if ($subscribedCourses) {
            $courses                = json_decode($subscribedCourses->courses_json, true);
            $totalSubscribedCourses = count($courses['academic_courses']) + count($courses['non_academic_courses']);
        }

        $studentDetails = [
            'name'                        => ucwords($user->name),
            'image'                       => $user->image ? Storage::url('uploads/user/profile_image/' . $user->image) : asset('frontend/images/default-image.jpg'),
            'class'                       => Auth::user()?->studentDetails?->className?->name ?? null,
            'plan_start'                  => '12/02/2023',
            'plan_expiry'                 => '12/02/2023',
            'parent_name'                 => optional($user->studentDetails)->parent_name ? ucwords($user->studentDetails->parent_name) : 'N/A',
            'subscribed_courses'          => 4,
            'completed_tasks'             => 12,
            'totalSubscribedCourses'      => $totalSubscribedCourses ?? '0',
            'subscribedCourses'           => $subscribedCourses,
            'totalAcadCourses'            => $totalAcadCourses,
            'totalNonAcadCourses'         => $totalNonAcadCourses,
            'completedAcadCourses'        => $completedAcadCourses,
            'completedNonAcadCourses'     => $completedNonAcadCourses,
            'acadCompletionPercentage'    => round($acadCompletionPercentage, 2),
            'nonAcadCompletionPercentage' => round($nonAcadCompletionPercentage, 2),
        ];

        Session::put('student_overview', $studentDetails);
    }
}
