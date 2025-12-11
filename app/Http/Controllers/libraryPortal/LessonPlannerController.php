<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileController;
use App\Models\AccessCode;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\MediaFiles;
use App\Models\Medium;
use App\Models\Planner;
use App\Models\SchoolAssignedClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolClass;
use App\Models\SchoolComplimentaryCourse;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class LessonPlannerController extends Controller
{
    //
    public $data = [];

    public function lessonPlanner(Request $request)
    {
        try {

            $role                   = getUserRoles();
            $medium                 = $request->query('medium');
            $parentId               = Auth::id();
            $teacherAssignedClasses = [];

            // If the role is "school_teacher", use school_id from UserAdditionalDetail
            if ($role == "school_teacher") {
                $parentId               = Auth::user()->userAdditionalDetail->school_id;
                $teacherAssignedClasses = getTeacherAssignedClasses();
            }

            // dd($teacherAssignedClasses);
            // Build the query
            if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1) {
                $query = AccessCode::with('class')
                    ->select('class_id')
                    ->where('school_id', $parentId)
                    ->groupBy('class_id');

                // Filter by medium if provided
                if ($medium) {
                    $query->where('medium_id', $medium);
                }
                // Filter by teacher's assigned classes if the role is "school_teacher"
                if ($role === 'school_teacher' && ! empty($teacherAssignedClasses)) {
                    $query->whereIn('class_id', $teacherAssignedClasses);
                }
            } else {
                $query = SchoolAssignedClass::where('school_id', $parentId)->select('class_id')->groupBy('class_id');

                // Filter by teacher's assigned classes if the role is "school_teacher"
                if ($role === 'school_teacher' && ! empty($teacherAssignedClasses)) {
                    $query->whereIn('class_id', $teacherAssignedClasses);
                }
            }

            $this->data['classCourses']        = $query->get();
            $this->data['medium']              = Medium::where('is_active', 1)->get();
            $this->data['complimentaryCourse'] = SchoolComplimentaryCourse::where('school_id', $parentId)->with('courses')->get();
            // dd($this->data['complimentaryCourse']);

            return view('schoolPortal.lessonPlanner.index', $this->data);
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
                ->toArray();         // Convert collection to array

            $allSubjectIds = [];

            // Fetch assigned subjects from digital content
            $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $parentId)
                ->where('class_id', $id)
                ->get();

            foreach ($schoolAssignedDigitalContent as $digitalContent) {
                $allSubjectIds = array_merge($allSubjectIds, explode(',', $digitalContent->subject_id));
            }

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
            return view('schoolPortal.lessonPlanner.class-subject', $this->data);

            // return view('schoolPortal.myCourses.class-subject', $this->data);
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

            $this->data['courseListing'] = $query->get();

            $this->data['totalAccessCodes']  = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->count();
            $this->data['unUsedAccessCodes'] = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->where('user_id', null)->count();
            $this->data['id']                = $id;
            $this->data['class_id']          = $class_id;
            $this->data['subjectName'] = Subject::where('id', $id)->where('is_active', 1)->value('name');
            $this->data['className'] = SchoolClass::where('id', $class_id)->where('is_active', 1)->value('name');

            return view('schoolPortal.lessonPlanner.course-listing', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }
    public function courseListingOLD($id, $class_id)
    {

        try {
            $this->data['courseListing'] = Course::where('is_active', 1)->with([
                'totalChapters',
                'metadataValues' => function ($query) {
                    $query->select('course_id', 'field_name', 'field_value');
                },
                'metadataValues.classInfo',
            ])
                ->where('category_id', 1)
                ->whereHas('metadataValues', function ($query) use ($id) {
                    $query->where('field_name', 'subject')
                        ->where('field_value', $id);
                })
                ->whereHas('metadataValues', function ($query) use ($class_id) {
                    $query->where('field_name', 'class')
                        ->where('field_value', $class_id);
                })
                ->get();

            $this->data['totalAccessCodes']  = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->count();
            $this->data['unUsedAccessCodes'] = AccessCode::where('school_id', Auth::id())->where('class_id', $id)->where('user_id', null)->count();
            $this->data['id']                = $id;
            $this->data['class_id']          = $class_id;

            // return $this->data['courseListing'];
            return view('schoolPortal.lessonPlanner.course-listing', $this->data);
        } catch (\Exception $e) {

            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function chapterDetails($id, $classId, $subjectId)
    {

        try {
            $this->data['id'] = $id;
            $this->data['classId'] = $classId;
            $this->data['subjectId'] = $subjectId;
            $this->data['subjectName'] = Subject::where('id', $subjectId)->where('is_active', 1)->value('name');
            $this->data['className'] = SchoolClass::where('id', $classId)->where('is_active', 1)->value('name');
            $this->data['courseName'] = Course::where('id', $id)->where('is_active', 1)->value('course_name');

            $this->data['plannerLesson'] = CourseChapter::with('user')->where('course_id', $id)->orderBy('sort_order', 'ASC')->get();
            return view('schoolPortal.lessonPlanner.chapter_details', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }
    public function chapterPlanner($id, $course_id, $subject_id, $class_id)
    {
        try {
            $this->data['courseId'] = $course_id;
            $this->data['subjectId'] = $subject_id;
            $this->data['classId'] = $class_id;
            $plannerLesson = Planner::whereRaw("FIND_IN_SET(?, chapter_id)", [$id])->with('details', 'class', 'subject', 'board', 'medium', 'series')->first();
            if (! $plannerLesson) {
                return redirect()->back()->with(['error' => config('constants.FLASH_REC_NOT_FOUND')]);
            }
            $groupedDetails = $plannerLesson->details->groupBy('type');

            $this->data['groupedDetails'] = $groupedDetails;
            $this->data['plannerLesson']  = $plannerLesson;
            $this->data['digitalContent'] = CourseChapter::with('chapters', 'folder', 'documents')->where('id', $id)->first();
            foreach ($this->data['digitalContent']->chapters as $chapter) {
                if (in_array($chapter->file_extension, ['mp4', 'avi', 'mov', 'm4v', 'm4p', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'm2v', 'wmv', 'flv', 'mkv', 'webm', '3gp', '3gp', 'm2ts', 'ogv', 'ts', 'mxf'])) {
                    $chapter->signed_url = FileController::sendOnSignedRoute($chapter->attachment_file);
                }
            }
            // dd($this->data['digitalContent']);
            $this->data['supportingFiles'] = MediaFiles::where('tbl_id', $id)->where('type', 'course_chapter_extra')->get();
            $this->data['folderId']        = $id;
            $startDate                     = Carbon::parse($plannerLesson->start_date);
            $completionDate                = Carbon::parse($plannerLesson->completion_date);
            $totalDays                     = $startDate->diffInDays($completionDate) + 1;
            $sundaysCount                  = 0;
            $currentDate                   = $startDate->copy();
            while ($currentDate->lte($completionDate)) {
                if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                    $sundaysCount++;
                }
                $currentDate->addDay();
            }
            $daysWithoutSundays = $totalDays - $sundaysCount;
            $percentagePerDay   = $daysWithoutSundays > 0 ? 100 / $daysWithoutSundays : 0;
            $today              = Carbon::today();

            if ($today->lt($startDate)) {
                $actualPercentage = 0;
            } elseif ($today->gt($completionDate)) {
                $actualPercentage = 100;
            } else {
                $completedDays = 0;
                $currentDate   = $startDate->copy();
                while ($currentDate->lte($today) && $currentDate->lte($completionDate)) {
                    if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                        $completedDays++;
                    }
                    $currentDate->addDay();
                }
                $actualPercentage = round($completedDays * $percentagePerDay, 2);
            }

            $this->data['actualPercentage'] = $actualPercentage;
            $this->data['percentagePerDay'] = $percentagePerDay;
            $this->data['startDate']        = $startDate;
            $this->data['completionDate']   = $completionDate;
            return view('schoolPortal.lessonPlanner.chapter_planner', $this->data);
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH', $e)]);
        }
    }
    public function downloadSupportingDocuments($id)
    {
        $supportingFiles = MediaFiles::where('tbl_id', $id)->where('type', 'course_chapter_extra')->get();
        // dd($supportingFiles,$id);
        if ($supportingFiles->isEmpty()) {
            return redirect()->back()->with('error', 'No documents available for download.');
        }

        $zipFileName = 'supporting-documents.zip';
        $zip         = new ZipArchive;
        $zipPath     = storage_path('app/' . $zipFileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($supportingFiles as $file) {
                $filePath = storage_path('app/public/uploads/course_chapter_files/' . $file->attachment_file);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->original_name); // Add file with original name
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Could not create ZIP file.');
        }

        // Read the ZIP file into memory
        $zipContents = file_get_contents($zipPath);

        // Create the response
        $response = Response::make($zipContents, 200, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
        ]);

        // Register a shutdown function to delete the temporary ZIP file after the response is sent
        register_shutdown_function(function () use ($zipPath) {
            if (file_exists($zipPath)) {
                unlink($zipPath); // Delete the temporary ZIP file
            }
        });

        return $response;
    }
}
