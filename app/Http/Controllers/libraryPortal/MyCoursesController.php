<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileController;
use App\Models\AccessCode;
use App\Models\AccessCodeLog;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\MediaFiles;
use App\Models\Medium;
use App\Models\SchoolAssignedClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolClass;
use App\Models\SchoolComplimentaryCourse;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserAdditionalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCoursesController extends Controller
{
    //
    public $data = [];

    public function myCourses(Request $request)
    {
        try {
            $role                   = getUserRoles();
            $medium                 = $request->query('medium');
            $parentId               = Auth::id();
            $teacherAssignedClasses = [];
            $teacherAssignedSubjects = [];

            if ($role === 'school_teacher') {
                $parentId = Auth::user()->userAdditionalDetail->school_id;
                $teacherAssignedClasses = getTeacherAssignedClasses();
                $teacherAssignedSubjects = getTeacherAssignedSubjects();
            }

            if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1) {
                $query = AccessCode::with('class')
                    ->select('class_id')
                    ->where('school_id', $parentId)
                    ->groupBy('class_id');

                if ($medium) {
                    $query->where('medium_id', $medium);
                }
            } else {
                $query = SchoolAssignedClass::with('class')
                    ->where('school_id', $parentId)
                    ->select('class_id')
                    ->groupBy('class_id');
            }

            if ($role === 'school_teacher' && !empty($teacherAssignedClasses)) {
                $query->whereIn('class_id', $teacherAssignedClasses);
            }

            $classCourses = $query->get();

            foreach ($classCourses as $classCourse) {
                $classId = $classCourse->class_id;

                $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $parentId)
                    ->where('class_id', $classId)
                    ->pluck('series_id')
                    ->toArray();

                $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $parentId)
                    ->where('class_id', $classId)
                    ->whereIn('series_id', $schoolAssignedSeries)
                    ->get();

                $allSubjectIds = [];
                foreach ($schoolAssignedDigitalContent as $digitalContent) {
                    $allSubjectIds = array_merge($allSubjectIds, explode(',', $digitalContent->subject_id));
                }

                $uniqueSubjectIds = array_unique($allSubjectIds);

                $subjectsWithCourses = [];

                // Load related courses (only if not using Access Code logic)
                if (config('COURSES_FILTER_BY_ACCESS_CODE') != 1) {
                    $courseData = Course::where('is_active', 1)
                        ->where('category_id', 1)
                        ->where(function ($query) use ($schoolAssignedDigitalContent, $classId) {
                            foreach ($schoolAssignedDigitalContent as $digitalContent) {
                                $subjectIds = explode(',', $digitalContent->subject_id);
                                $seriesId = $digitalContent->series_id;

                                $query->orWhere(function ($q) use ($subjectIds, $seriesId, $classId) {
                                    $q->whereHas('metadataValues', function ($q1) use ($subjectIds) {
                                        $q1->where('field_name', 'subject')->whereIn('field_value', $subjectIds);
                                    })
                                        ->whereHas('metadataValues', function ($q2) use ($seriesId) {
                                            $q2->where('field_name', 'series')->where('field_value', $seriesId);
                                        })
                                        ->whereHas('metadataValues', function ($q3) use ($classId) {
                                            $q3->where('field_name', 'class')->where('field_value', $classId);
                                        });
                                });
                            }
                        })
                        ->with(['metadataValues' => function ($query) {
                            $query->whereIn('field_name', ['subject', 'book_cover_image', 'thumbnail_image']);
                        }])
                        ->get();

                    foreach ($courseData as $course) {
                        $subjectId      = $course->metadataValues->where('field_name', 'subject')->pluck('field_value')->first();
                        $bannerImage    = $course->metadataValues->where('field_name', 'book_cover_image')->pluck('field_value')->first();
                        $thumbnailImage = $course->metadataValues->where('field_name', 'thumbnail_image')->pluck('field_value')->first();

                        if ($subjectId) {
                            $subjectsWithCourses[$subjectId] = [
                                'id'               => $course->id,
                                'course_name'      => $course->course_name,
                                'book_cover_image' => $bannerImage,
                                'thumbnail_image'  => $thumbnailImage,
                            ];
                        }
                    }
                }

                // Load subjects
                $subjectQuery = Subject::whereIn('id', $uniqueSubjectIds);
                if ($role === 'school_teacher' && !empty($teacherAssignedSubjects)) {
                    $subjectQuery->whereIn('id', $teacherAssignedSubjects);
                }

                $subjects = $subjectQuery->get();

                foreach ($subjects as $subject) {
                    $subjectId = $subject->id;
                    if (isset($subjectsWithCourses[$subjectId])) {
                        $subject->course_id        = $subjectsWithCourses[$subjectId]['id'];
                        $subject->course_name      = $subjectsWithCourses[$subjectId]['course_name'];
                        $subject->book_cover_image = $subjectsWithCourses[$subjectId]['book_cover_image'];
                        $subject->thumbnail_image  = $subjectsWithCourses[$subjectId]['thumbnail_image'];
                    } else {
                        $subject->course_id        = null;
                        $subject->course_name      = null;
                        $subject->book_cover_image = null;
                        $subject->thumbnail_image  = null;
                    }
                }

                $classCourse->subjects = $subjects;
            }

            $this->data['classCourses'] = $classCourses;
            $this->data['medium'] = Medium::where('is_active', 1)->get();

            $this->data['complimentaryCourse'] = SchoolComplimentaryCourse::where('school_id', $parentId)->where('category_id', 2)
                ->whereHas('courses', function ($query) {
                    $query->whereHas('metadataValues', function ($q) {
                        $q->where('field_name', 'available_for_complimentary_package')
                            ->whereIn('field_value', ['all', '1']);
                    });
                })
                ->with(['courses' => function ($query) {
                    $query->whereHas('metadataValues', function ($q) {
                        $q->where('field_name', 'available_for_complimentary_package')
                            ->whereIn('field_value', ['all', '1']);
                    });
                }])
                ->get();
            $this->data['filteredClassActivityCourses'] = SchoolComplimentaryCourse::where('school_id', $parentId)->where('category_id', 1)
                ->with('courses')
                ->get();
            return view('schoolPortal.myCourses.my-courses', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }


    public function classSubject($id)
    {
        try {
            $role                    = getUserRoles();
            $parentId                = Auth::id();
            $teacherAssignedSubjects = [];

            if ($role == "school_teacher") {
                $parentId                = Auth::user()->userAdditionalDetail->school_id;
                $teacherAssignedSubjects = getTeacherAssignedSubjects();
            }
            $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $parentId)
                ->where('class_id', $id)
                ->pluck('series_id') // Multiple rows → Get all series assigned
                ->toArray();
            // Convert collection to array

            $allSubjectIds = [];

            // Fetch assigned subjects from digital content
            $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $parentId)
                ->where('class_id', $id)
                ->whereIn('series_id', $schoolAssignedSeries)
                ->get();

            foreach ($schoolAssignedDigitalContent as $digitalContent) {
                $allSubjectIds = array_merge($allSubjectIds, explode(',', $digitalContent->subject_id));
            }
            // DD($schoolAssignedDigitalContent)  ;       

            $uniqueSubjectIds       = array_unique($allSubjectIds);
            $schoolAssignedSubjects = array_values($uniqueSubjectIds);

            $this->data['classId']   = $id;
            $this->data['className'] = SchoolClass::where('id', $id)->value('name');

            if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1) {
                $this->data['accessCodes'] = AccessCode::where('school_id', $parentId)->where('class_id', $id)->get();
                $subjectIds                = [];

                foreach ($this->data['accessCodes'] as $code) {
                    $subjectIds = array_merge($subjectIds, explode(',', $code->subject_id));
                }

                // Filter subjects based on the teacher's assigned subjects if the role is "school_teacher"
                $subjectQuery = Subject::whereIn('id', $subjectIds);

                if ($role == "school_teacher" && ! empty($teacherAssignedSubjects)) {
                    $subjectQuery->whereIn('id', $teacherAssignedSubjects);
                }

                $this->data['subjects']             = $subjectQuery->get();
                $this->data['totalAccessCodes']     = AccessCode::where('school_id', $parentId)->where('class_id', $id)->count();
                $this->data['unUsedAccessCodes']    = AccessCode::where('school_id', $parentId)->where('class_id', $id)->where('user_id', null)->count();
                $this->data['occcupiedAccessCodes'] = AccessCode::where('school_id', $parentId)->where('class_id', $id)->whereNotNull('user_id')->count();
                $this->data['remainingAccessCodes'] = $this->data['totalAccessCodes'] - $this->data['occcupiedAccessCodes'];
                $this->data['redeemedAccessCode']   = AccessCode::where('school_id', $parentId)->where('class_id', $id)->whereNotNull('user_id')->with('usedAccessCodes', 'accessCodeLog')->get();
                $this->data['unRedeemedAccessCode'] = AccessCode::where('school_id', $parentId)->where('class_id', $id)->where('user_id', null)->with('usedAccessCodes', 'accessCodeLog')->get();

                $this->data['users'] = User::with(['userAdditionalDetail', 'studentDetails'])
                    ->whereHas('userAdditionalDetail', function ($query) use ($parentId) {
                        $query->where('role', 'school_student')
                            ->where('school_id', $parentId);
                    })->whereHas('studentDetails', function ($query) use ($id) {
                        $query->where('class', $id);
                    })->whereDoesntHave('accessCodes')->get();
            } else {
                $courseData = Course::where('is_active', 1)
                    ->where('category_id', 1)
                    ->where(function ($query) use ($schoolAssignedDigitalContent, $id) {
                        foreach ($schoolAssignedDigitalContent as $digitalContent) {
                            $subjectIds = explode(',', $digitalContent->subject_id);
                            $seriesId = $digitalContent->series_id;

                            $query->orWhere(function ($q) use ($subjectIds, $seriesId, $id) {
                                $q->whereHas('metadataValues', function ($q1) use ($subjectIds) {
                                    $q1->where('field_name', 'subject')->whereIn('field_value', $subjectIds);
                                })
                                    ->whereHas('metadataValues', function ($q2) use ($seriesId) {
                                        $q2->where('field_name', 'series')->where('field_value', $seriesId);
                                    })
                                    ->whereHas('metadataValues', function ($q3) use ($id) {
                                        $q3->where('field_name', 'class')->where('field_value', $id);
                                    });
                            });
                        }
                    })
                    ->with(['metadataValues' => function ($query) {
                        $query->whereIn('field_name', ['subject', 'book_cover_image', 'thumbnail_image']);
                    }])
                    ->get();

                $subjectsWithCourses = [];

                foreach ($courseData as $course) {
                    $subjectId      = $course->metadataValues->where('field_name', 'subject')->pluck('field_value')->first();
                    $bannerImage    = $course->metadataValues->where('field_name', 'book_cover_image')->pluck('field_value')->first();
                    $thumbnailImage = $course->metadataValues->where('field_name', 'thumbnail_image')->pluck('field_value')->first();

                    if ($subjectId) {
                        $subjectsWithCourses[$subjectId] = [
                            'id'               => $course->id,
                            'course_name'      => $course->course_name,
                            'book_cover_image' => $bannerImage,
                            'thumbnail_image'  => $thumbnailImage,
                        ];
                    }
                }

                if (! empty($schoolAssignedSubjects)) {
                    $subjectQuery = Subject::with('course');
                    if ($role == "school_admin") {
                        $subjectQuery->whereIn('id', $schoolAssignedSubjects);
                    } elseif ($role == "school_teacher" && ! empty($teacherAssignedSubjects)) {
                        $commonAssignedSubjects = array_intersect($schoolAssignedSubjects, $teacherAssignedSubjects);
                        $subjectQuery->whereIn('id', $commonAssignedSubjects);
                    }

                    $subjects = $subjectQuery->get();

                    foreach ($subjects as $subject) {
                        $subjectId = $subject->id;
                        if (isset($subjectsWithCourses[$subjectId])) {
                            $subject->course_id        = $subjectsWithCourses[$subjectId]['id'];
                            $subject->course_name        = $subjectsWithCourses[$subjectId]['course_name'];
                            $subject->book_cover_image = $subjectsWithCourses[$subjectId]['book_cover_image'];
                            $subject->thumbnail_image  = $subjectsWithCourses[$subjectId]['thumbnail_image'];
                        } else {
                            $subject->course_id        = null;
                            $subject->course_name        = null;
                            $subject->book_cover_image = null;
                            $subject->thumbnail_image  = null;
                        }
                    }

                    $this->data['subjects'] = $subjects;
                } else {
                    $this->data['subjects'] = collect([]);
                }
            }

            return view('schoolPortal.myCourses.class-subject', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function fetchClassSubjects($id)
    {
        try {
            $role = getUserRoles();
            $parentId = Auth::id();
            $teacherAssignedSubjects = [];

            if ($role == "school_teacher") {
                $parentId = Auth::user()->userAdditionalDetail->school_id;
                $teacherAssignedSubjects = getTeacherAssignedSubjects();
            }

            // Fetch assigned subjects from digital content
            $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $parentId)
                ->where('class_id', $id)
                ->get();

            $allSubjectIds = [];
            foreach ($schoolAssignedDigitalContent as $digitalContent) {
                $allSubjectIds = array_merge($allSubjectIds, explode(',', $digitalContent->subject_id));
            }

            // Ensure unique subject IDs
            $uniqueSubjectIds = array_unique($allSubjectIds);

            // Fetch subjects based on the unique IDs
            $subjectQuery = Subject::whereIn('id', $uniqueSubjectIds);

            if ($role == "school_teacher" && !empty($teacherAssignedSubjects)) {
                $subjectQuery->whereIn('id', $teacherAssignedSubjects);
            }

            $subjects = $subjectQuery->get();

            // Prepare data for the view
            $this->data['classId'] = $id;
            $this->data['className'] = SchoolClass::where('id', $id)->value('name');
            $this->data['subjects'] = $subjects;

            return view('schoolPortal.myCourses.acc-class-subjects', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }



    public function courseListing($id, $class_id)
    {
        try {
            $role     = getUserRoles();
            $board    = getUserBoard();
            $medium   = getUserMedium();
            $parentId = Auth::id();

            if ($role == "school_teacher") {
                $parentId = Auth::user()->userAdditionalDetail->school_id;
            }

            // $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $parentId)
            //     ->where('class_id', $class_id)
            //     ->value('series_id');
            $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $parentId)
                ->where('class_id', $class_id)
                ->whereRaw("FIND_IN_SET(?, subject_id)", [$id])
                ->pluck('series_id')
                ->first();

            $query = Course::where('is_active', 1)
                ->with([
                    'totalChapters',
                    'metadataValues' => function ($query) {
                        $query->select('course_id', 'field_name', 'field_value');
                    },
                    'metadataValues.classInfo',
                ])
                ->where('category_id', 1)
                ->whereHas('metadataValues', function ($query) use ($schoolAssignedSeries) {
                    $query->where('field_name', 'series')->where('field_value', $schoolAssignedSeries);
                })
                ->whereHas('metadataValues', function ($query) use ($id) {
                    $query->where('field_name', 'subject')->where('field_value', $id);
                })
                ->whereHas('metadataValues', function ($query) use ($class_id) {
                    $query->where('field_name', 'class')->where('field_value', $class_id);
                });

            // Apply board filter only if board is not 0
            // if ($board != 0) {
            //     $query->whereHas('metadataValues', function ($query) use ($board) {
            //         $query->where('field_name', 'board')->where('field_value', $board);
            //     });
            // }

            // // Apply medium filter only if medium is not 0
            // if ($medium != 0) {
            //     $query->whereHas('metadataValues', function ($query) use ($medium) {
            //         $query->where('field_name', 'medium')->where('field_value', $medium);
            //     });
            // }


            // dd($board, $medium);

            $this->data['courseListing'] = $query->get();
            // dd($this->data['courseListing']);

            $this->data['totalAccessCodes']  = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->count();
            $this->data['unUsedAccessCodes'] = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->where('user_id', null)->count();
            $this->data['id']                = $id;
            $this->data['subjectName']       = Subject::where('id', $id)->where('is_active', 1)->value('name');
            $this->data['class_id']          = $class_id;
            $this->data['className']         = SchoolClass::where('id', $class_id)->where('is_active', 1)->value('name');

            return view('schoolPortal.myCourses.course-listing', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function classAccessCodeView($id)
    {
        try {
            $this->data['accessCodes']          = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->get();
            $this->data['totalAccessCodes']     = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->get();
            $this->data['unUsedAccessCodes']    = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->where('user_id', null)->count();
            $this->data['occcupiedAccessCodes'] = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->whereNotNull('user_id')->count();
            $this->data['remainingAccessCodes'] = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->where('user_id', null)->get();
            $this->data['redeemedAccessCode']   = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->whereNotNull('user_id')->with('usedAccessCodes', 'accessCodeLog')->get();
            $this->data['unRedeemedAccessCode'] = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->where('user_id', null)->with('usedAccessCodes', 'accessCodeLog')->get();
            $this->data['users']                = User::with(['userAdditionalDetail', 'studentDetails'])
                ->whereHas('userAdditionalDetail', function ($query) {
                    $query->where('role', 'school_student')
                        ->where('school_id', Auth::id());
                })->whereHas('studentDetails', function ($query) use ($id) {
                    $query->where('class', $id);
                })->whereDoesntHave('accessCodes')->get();
            return view('schoolPortal.myCourses.access-code', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }
    public function assignAccessCodes(Request $request)
    {
        try {
            $validated = $request->validate([
                'data'               => 'required|array',
                'data.*.user_id'     => 'required|integer|exists:users,id',
                'data.*.access_code' => 'required|string|exists:access_codes,access_code',
            ]);

            $accessCodeData = $request['data'];

            foreach ($accessCodeData as $item) {
                $userId           = $item['user_id'];
                $accessCode       = $item['access_code'];
                $accessCodeRecord = AccessCode::where('access_code', $accessCode)->first();
                if ($accessCodeRecord && ! $accessCodeRecord->user_id) {
                    $accessCodeRecord->user_id = $userId;
                    $accessCodeRecord->status  = 'active';
                    $accessCodeRecord->save();

                    AccessCodeLog::create([
                        'user_id'   => $userId,
                        'title'     => 'Access Code Activated',
                        'action_as' => 'user_access_code_actived_by_school',
                        'action_by' => auth()->id(),
                        'json_data' => json_encode([
                            $accessCodeRecord,
                        ]),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Access codes assigned successfully.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function coursesDetails(Request $request, $id, $classId, $subjectId)
    {
        try {
            $this->data['classId']     = $classId;
            $this->data['subjectId']   = $subjectId;
            $this->data['subjectName'] = Subject::where('id', $subjectId)->where('is_active', 1)->value('name');
            $this->data['className']   = SchoolClass::where('id', $classId)->where('is_active', 1)->value('name');
            $this->data['courseName']  = Course::where('id', $id)->where('is_active', 1)->value('course_name');

            $limit = $request->input('limit', 10); // Default to 10 if no limit is provided
            $query = CourseChapter::with('chapterListing', 'folder', 'documents', 'resources')
                ->where('course_id', $id)
                ->orderBy('sort_order', 'asc');
            if ($limit === 'all') {
                $chapters = $query->get();
            } else {
                $limit = is_numeric($limit) ? (int)$limit : 10; // default safeguard
                $chapters = $query->paginate($limit)->appends($request->all());
            }

            $this->data['chapters'] = $chapters;


            if ($this->data['chapters']->isEmpty()) {
                return redirect()->back()->with(['error' => config('constants.FLASH_REC_NOT_FOUND')]);
            }
            // dd($this->data);
            // Generate signed URLs for videos
            foreach ($this->data['chapters'] as $chapter) {
                foreach ($chapter->chapterListing as $file) {
                    if (in_array($file->file_extension, ['mp4', 'avi', 'mov', 'm4v', 'm4p', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'm2v', 'wmv', 'flv', 'mkv', 'webm', '3gp', '3gp', 'm2ts', 'ogv', 'ts', 'mxf'])) {
                        $file->signed_url = FileController::sendOnSignedRoute($file->attachment_file);
                    }
                }
            }
            return view('schoolPortal.myCourses.courses-details', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }
}
