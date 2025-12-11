<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileController;
use App\Models\CourseChapter;
use App\Models\MediaFiles;
use App\Models\MediaFolder;
use App\Models\Planner;
use App\Models\PlannerOff;
use App\Models\SchoolAssignedClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolCompletedPlanner;
use App\Models\SchoolPlannerVisibility;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use ZipArchive;

class DailyPlannerController extends Controller
{
    public $data = [];
    public function getPlanner(Request $request)
    {
        try {
            $parentId                = Auth::id();
            $role                    = getUserRoles();
            $schoolId                = Auth::id();
            $teacherAssignedClasses  = [];
            $teacherAssignedSubjects = [];
            $userBoard               = getUserBoard();
            $userMedium              = getUserMedium();

            // If the role is "school_teacher", set school_id and fetch assigned classes and subjects
            if ($role === "school_teacher") {
                $schoolId                = Auth::user()->userAdditionalDetail->school_id;
                $teacherAssignedClasses  = getTeacherAssignedClasses();
                $teacherAssignedSubjects = getTeacherAssignedSubjects();
            }

            // Get school assigned classes and digital content
            $schoolAssignedClasses = SchoolAssignedClass::where('school_id', $schoolId)->pluck('class_id')->toArray();
            //this is for the planner visibilty
            $existingPlannerVisibilty = SchoolPlannerVisibility::where('school_id', Auth::id())->get();


            // Get first to check if we have class_id in request
            $firstQuery = Planner::when($userBoard != 0, function ($query) use ($userBoard) {
                return $query->where('board_id', $userBoard);
            })
                ->when($userMedium != 0, function ($query) use ($userMedium) {
                    return $query->where('medium_id', $userMedium);
                })
                ->with(['class', 'subject', 'chapter'])
                ->when($role === "school_teacher", function ($query) use ($teacherAssignedClasses) {
                    if (!empty($teacherAssignedClasses)) {
                        $query->whereIn('class_id', $teacherAssignedClasses);
                    }
                })
                ->when($role === "school_admin", function ($query) use ($schoolAssignedClasses) {
                    $query->whereIn('class_id', $schoolAssignedClasses);
                })
                ->when($request->query('type') && $request->query('type') !== 'all', function ($query) use ($request) {
                    $query->where('type', $request->query('type'));
                })
                ->orderBy('class_id');

            // Get all classes that have planners (with filters applied)
            $classes = $firstQuery->get();

            if ($classes->isEmpty()) {
                return view('schoolPortal.planner.index');
            }

            $firstclass = $classes->first();
            $classId    = $request->query('class_id') ?? $firstclass->class_id;

            // Get school assigned digital content for this class
            $schoolAssignedDigitalContent = SchoolAssignedDigitalContent::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->first();

            $schoolAssignedDigitalContentAll = SchoolAssignedDigitalContent::where('school_id', $schoolId)
                ->whereNotNull('subject_id')
                ->get();
            // Initialize arrays
            $assignedSeriesClassSubjects = [];

            foreach ($schoolAssignedDigitalContentAll as $content) {
                $subjects = explode(',', $content->subject_id); // handle multiple subjects

                foreach ($subjects as $subjectId) {
                    $assignedSeriesClassSubjects[] = [
                        'class_id' => $content->class_id,
                        'series_id' => $content->series_id,
                        'subject_id' => (int) trim($subjectId),
                    ];
                }
            }
            $schoolAssignedDigitalContentSub = SchoolAssignedDigitalContent::where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->get();
            $schoolAssignedSubjects = [];
            $schoolAssignedSeries = [];

            if ($schoolAssignedDigitalContent) {
                $schoolAssignedSeries = explode(',', $schoolAssignedDigitalContent->series_id);
            }
            // Get all unique subject IDs from all rows
            if ($schoolAssignedDigitalContentSub) {
                foreach ($schoolAssignedDigitalContentSub as $content) {
                    if (!empty($content->subject_id)) {
                        $subjectsInRow = explode(',', $content->subject_id);
                        $schoolAssignedSubjects = array_merge($schoolAssignedSubjects, $subjectsInRow);
                    }
                }
            }
            // Remove duplicates and re-index the array
            $schoolAssignedSubjects = array_values(array_unique($schoolAssignedSubjects));
            // Base query for planner type detection
            $getPlannerTypeQuery = Planner::when($userBoard != 0, function ($query) use ($userBoard) {
                return $query->where('board_id', $userBoard);
            })
                ->when($userMedium != 0, function ($query) use ($userMedium) {
                    return $query->where('medium_id', $userMedium);
                })
                ->where('class_id', $classId)
                ->when($request->query('type') && $request->query('type') !== 'all', function ($query) use ($request) {
                    $query->where('type', $request->query('type'));
                });

            $getPlannerType = $getPlannerTypeQuery->first();
            $plannerType = $getPlannerType ? $getPlannerType->type : null;

            if ($plannerType == null) {
                return view('schoolPortal.planner.index');
            }
            // View Daily Planner
            if ($plannerType == 'daily') {
                $plannerDatesQuery = Planner::where(function ($query) use ($schoolId, $role, $teacherAssignedClasses, $teacherAssignedSubjects, $schoolAssignedClasses, $schoolAssignedSubjects, $schoolAssignedSeries) {
                    // School-specific planners
                    $query->where('school_id', $schoolId);

                    // Or universal planners that match school's assigned subjects and series
                    if (!empty($schoolAssignedSubjects)) {
                        $query->orWhere(function ($q) use ($schoolAssignedSubjects, $schoolAssignedSeries) {
                            $q->whereNull('school_id')
                                ->whereIn('subject_id', $schoolAssignedSubjects);

                            if (!empty($schoolAssignedSeries)) {
                                $q->whereIn('series_id', $schoolAssignedSeries);
                            }
                        });
                    }

                    // If teacher, filter by assigned classes and subjects
                    if ($role === "school_teacher") {
                        if (!empty($teacherAssignedClasses)) {
                            $query->whereIn('class_id', $teacherAssignedClasses);
                        }
                        if (!empty($teacherAssignedSubjects)) {
                            $query->whereIn('subject_id', $teacherAssignedSubjects);
                        }
                    }
                    if ($role === "school_admin") {
                        if (!empty($schoolAssignedClasses)) {
                            $query->whereIn('class_id', $schoolAssignedClasses);
                        }
                        if (!empty($schoolAssignedSubjects)) {
                            $query->whereIn('subject_id', $schoolAssignedSubjects);
                        }
                    }
                })
                    ->where('class_id', $classId)
                    ->when($userBoard != 0, function ($query) use ($userBoard) {
                        return $query->where('board_id', $userBoard);
                    })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    });

                $plannerDates = $plannerDatesQuery->selectRaw('MIN(start_date) as start_date, MAX(completion_date) as completion_date')
                    ->first();

                // If no planner dates are found, fallback to the current month's start and end date
                $startDate = $plannerDates ? $plannerDates->start_date : now()->startOfMonth()->format('Y-m-d');
                $endDate   = $plannerDates ? $plannerDates->completion_date : now()->endOfMonth()->format('Y-m-d');

                $totalPlannerDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
                $currentDate      = now();

                // Weekday names for the header
                $weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                // Get all dates between start and end date, excluding Sundays
                $allDates = [];
                $current  = new DateTime($startDate);
                $end      = new DateTime($endDate);

                while ($current <= $end) {
                    if ($current->format('w') != 0) { // Exclude Sunday (0 corresponds to Sunday)
                        $allDates[] = clone $current;     // Add non-Sunday date
                    }
                    $current->modify('+1 day');
                }

                $totalDays = count($allDates); // Total number of non-Sunday days

                // Fetch planner data with school-specific filters
                $plannerDataQuery = Planner::with(['class', 'subject', 'chapter'])
                    ->where(function ($query) use ($schoolId, $role, $teacherAssignedClasses, $teacherAssignedSubjects, $schoolAssignedClasses, $schoolAssignedSubjects, $schoolAssignedSeries) {
                        // School-specific planners
                        $query->where('school_id', $schoolId);

                        // Or universal planners that match school's assigned subjects and series
                        if (!empty($schoolAssignedSubjects)) {
                            $query->orWhere(function ($q) use ($schoolAssignedSubjects, $schoolAssignedSeries) {
                                $q->whereNull('school_id')
                                    ->whereIn('subject_id', $schoolAssignedSubjects);

                                if (!empty($schoolAssignedSeries)) {
                                    $q->whereIn('series_id', $schoolAssignedSeries);
                                }
                            });
                        }

                        // If teacher, filter by assigned classes and subjects
                        if ($role === "school_teacher") {
                            if (!empty($teacherAssignedClasses)) {
                                $query->whereIn('class_id', $teacherAssignedClasses);
                            }
                            if (!empty($teacherAssignedSubjects)) {
                                $query->whereIn('subject_id', $teacherAssignedSubjects);
                            }
                        }
                        if ($role === "school_admin") {
                            if (!empty($schoolAssignedClasses)) {
                                $query->whereIn('class_id', $schoolAssignedClasses);
                            }
                            if (!empty($schoolAssignedSubjects)) {
                                $query->whereIn('subject_id', $schoolAssignedSubjects);
                            }
                        }
                    })
                    ->where('class_id', $classId)
                    ->when($userBoard != 0, function ($query) use ($userBoard) {
                        return $query->where('board_id', $userBoard);
                    })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    })
                    ->whereBetween('start_date', [$startDate, $endDate]);

                $plannerData = $plannerDataQuery->get();

                // Fetch planner_offs data for the authenticated school
                $plannerOffs = PlannerOff::whereHas('planner', function ($query) use ($schoolId, $role, $teacherAssignedClasses, $schoolAssignedClasses) {
                    $query->where('school_id', $schoolId)
                        ->orWhereNull('school_id'); // Include universal planners' holidays if needed

                    // If teacher, filter by assigned classes
                    if ($role === "school_teacher" && !empty($teacherAssignedClasses)) {
                        $query->whereIn('class_id', $teacherAssignedClasses);
                    }
                    if ($role === "school_admin" && !empty($schoolAssignedClasses)) {
                        $query->whereIn('class_id', $schoolAssignedClasses);
                    }
                })->pluck('date')->toArray();

                // Process planner data into day-wise structure (same as before)
                $dayWiseData = [];
                foreach ($plannerData as $item) {
                    $plannedDate    = new DateTime($item->start_date);
                    $completionDate = new DateTime($item->completion_date);

                    for ($i = 0; $i < $item->allotted_days; $i++) {
                        $boxDate          = (clone $plannedDate)->modify("+$i days");
                        $boxDateFormatted = $boxDate->format('Y-m-d');

                        if ($boxDate->format('w') != 0 && !in_array($boxDateFormatted, $plannerOffs)) { // Exclude Sunday and holiday dates
                            $day = array_search($boxDateFormatted, array_map(fn($date) => $date->format('Y-m-d'), $allDates));

                            if ($day !== false) {
                                $day += 1; // Convert to 1-based index for Blade usage

                                $boxClass = 'shiftBox';
                                if ($currentDate >= $boxDate && $currentDate <= $completionDate) {
                                    $boxClass .= ' lightgreen';
                                } elseif ($currentDate < $boxDate) {
                                    $boxClass .= ' lightorange';
                                } else {
                                    $boxClass .= ' lightred';
                                }

                                $existingChapters = $dayWiseData[$day][$item->subject_id] ?? [];
                                $duplicate        = false;

                                foreach ($existingChapters as $existingChapter) {
                                    if ($existingChapter['chapter_id'] == $item->chapter_id) {
                                        $duplicate = true;
                                        break;
                                    }
                                }

                                if (!$duplicate) {
                                    $title = $item->chapter->chapter_name ?? 'No Chapter Name';

                                    $dayWiseData[$day][$item->subject_id][] = [
                                        'chapter_id' => $item->chapter_id,
                                        'title'      => $title,
                                        'class'      => $boxClass,
                                    ];
                                }
                            }
                        }
                    }
                }

                // Fetch subjects with school-specific filters
                $subjectsQuery = Planner::select('class_id', 'subject_id')
                    ->with('subject')->distinct()
                    ->where(function ($query) use ($schoolId, $role, $teacherAssignedClasses, $teacherAssignedSubjects, $schoolAssignedClasses, $schoolAssignedSubjects, $schoolAssignedSeries) {
                        // School-specific planners
                        $query->where('school_id', $schoolId);

                        // Or universal planners that match school's assigned subjects and series
                        if (!empty($schoolAssignedSubjects)) {
                            $query->orWhere(function ($q) use ($schoolAssignedSubjects, $schoolAssignedSeries) {
                                $q->whereNull('school_id')
                                    ->whereIn('subject_id', $schoolAssignedSubjects);

                                if (!empty($schoolAssignedSeries)) {
                                    $q->whereIn('series_id', $schoolAssignedSeries);
                                }
                            });
                        }

                        // If teacher, filter by assigned classes and subjects
                        if ($role === "school_teacher") {
                            if (!empty($teacherAssignedClasses)) {
                                $query->whereIn('class_id', $teacherAssignedClasses);
                            }
                            if (!empty($teacherAssignedSubjects)) {
                                $query->whereIn('subject_id', $teacherAssignedSubjects);
                            }
                        }
                        if ($role === "school_admin") {
                            if (!empty($schoolAssignedClasses)) {
                                $query->whereIn('class_id', $schoolAssignedClasses);
                            }
                            if (!empty($schoolAssignedSubjects)) {
                                $query->whereIn('subject_id', $schoolAssignedSubjects);
                            }
                        }
                    })
                    ->when($userBoard != 0, function ($query) use ($userBoard) {
                        return $query->where('board_id', $userBoard);
                    })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    })
                    ->where('class_id', $classId);

                $subjects = $subjectsQuery->get();
                return view('schoolPortal.planner.index', compact('plannerType', 'existingPlannerVisibilty', 'schoolId', 'classes', 'totalPlannerDays', 'dayWiseData', 'subjects', 'weekDays', 'allDates', 'totalDays', 'startDate', 'endDate'));
            } elseif ($plannerType == 'weekly') {
                // Get planner data grouped by week with school-specific filters
                $plannerDatesQuery = Planner::when($userBoard != 0, function ($query) use ($userBoard) {
                    return $query->where('board_id', $userBoard);
                })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    })
                    ->where(function ($query) use ($schoolId, $role, $teacherAssignedClasses, $teacherAssignedSubjects, $schoolAssignedClasses, $schoolAssignedSubjects, $schoolAssignedSeries) {
                        // School-specific planners
                        $query->where('school_id', $schoolId);

                        // Or universal planners that match school's assigned subjects and series
                        if (!empty($schoolAssignedSubjects)) {
                            $query->orWhere(function ($q) use ($schoolAssignedSubjects, $schoolAssignedSeries) {
                                $q->whereNull('school_id')
                                    ->whereIn('subject_id', $schoolAssignedSubjects);

                                if (!empty($schoolAssignedSeries)) {
                                    $q->whereIn('series_id', $schoolAssignedSeries);
                                }
                            });
                        }

                        // If teacher, filter by assigned classes and subjects
                        if ($role === "school_teacher") {
                            if (!empty($teacherAssignedClasses)) {
                                $query->whereIn('class_id', $teacherAssignedClasses);
                            }
                            if (!empty($teacherAssignedSubjects)) {
                                $query->whereIn('subject_id', $teacherAssignedSubjects);
                            }
                        }
                        if ($role === "school_admin") {
                            if (!empty($schoolAssignedClasses)) {
                                $query->whereIn('class_id', $schoolAssignedClasses);
                            }
                            if (!empty($schoolAssignedSubjects)) {
                                $query->whereIn('subject_id', $schoolAssignedSubjects);
                            }
                        }
                    })
                    ->where('class_id', $classId);

                $plannerDates = $plannerDatesQuery->selectRaw('MIN(start_date) as start_date, MAX(completion_date) as completion_date')
                    ->first();

                // Define start and end dates
                $startDate = $plannerDates ? $plannerDates->start_date : now()->startOfMonth()->format('Y-m-d');
                $endDate   = $plannerDates ? $plannerDates->completion_date : now()->endOfMonth()->format('Y-m-d');

                // Get weekly breakdown
                $weeks       = [];
                $currentDate = Carbon::parse($startDate);
                while ($currentDate <= Carbon::parse($endDate)) {
                    $weekNumber = $currentDate->copy()->startOfWeek()->format('W');
                    if (!isset($weeks[$weekNumber])) {
                        $weeks[$weekNumber] = [
                            'start' => $currentDate->copy()->startOfWeek()->format('Y-m-d'),
                            'end'   => $currentDate->copy()->endOfWeek()->format('Y-m-d'),
                        ];
                    }
                    $currentDate->addWeek();
                }

                // Fetch planner data with school-specific filters
                $plannerDataQuery = Planner::with(['class', 'subject', 'chapter'])
                    ->where('class_id', $classId)
                    ->when($userBoard != 0, function ($query) use ($userBoard) {
                        return $query->where('board_id', $userBoard);
                    })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    })
                    ->where(function ($query) use ($schoolId, $role, $teacherAssignedClasses, $teacherAssignedSubjects, $schoolAssignedClasses, $schoolAssignedSubjects, $schoolAssignedSeries) {
                        // School-specific planners
                        $query->where('school_id', $schoolId);

                        // Or universal planners that match school's assigned subjects and series
                        if (!empty($schoolAssignedSubjects)) {
                            $query->orWhere(function ($q) use ($schoolAssignedSubjects, $schoolAssignedSeries) {
                                $q->whereNull('school_id')
                                    ->whereIn('subject_id', $schoolAssignedSubjects);

                                if (!empty($schoolAssignedSeries)) {
                                    $q->whereIn('series_id', $schoolAssignedSeries);
                                }
                            });
                        }

                        // If teacher, filter by assigned classes and subjects
                        if ($role === "school_teacher") {
                            if (!empty($teacherAssignedClasses)) {
                                $query->whereIn('class_id', $teacherAssignedClasses);
                            }
                            if (!empty($teacherAssignedSubjects)) {
                                $query->whereIn('subject_id', $teacherAssignedSubjects);
                            }
                        }
                        if ($role === "school_admin") {
                            if (!empty($schoolAssignedClasses)) {
                                $query->whereIn('class_id', $schoolAssignedClasses);
                            }
                            if (!empty($schoolAssignedSubjects)) {
                                $query->whereIn('subject_id', $schoolAssignedSubjects);
                            }
                        }
                    })
                    ->whereBetween('start_date', [$startDate, $endDate]);

                $plannerData = $plannerDataQuery->get();

                // Organize data into weeks
                $weekWiseData = [];
                foreach ($plannerData as $item) {
                    $chapterIds = explode(',', $item->chapter_id);                                                   // Convert string to array
                    $chapters   = CourseChapter::whereIn('id', $chapterIds)->pluck('chapter_name', 'id')->toArray(); // Get all chapter names

                    $startOfWeek = Carbon::parse($item->start_date)->startOfWeek()->format('W');

                    $weekWiseData[$startOfWeek][$item->subject_id][] = [
                        'palnner_id' => $item->palnner_id,
                        'chapter_id' => $chapterIds, // Store array of IDs
                        'titles'     => $chapters,   // Store an array of chapter names
                        'class'      => 'shiftBox',
                    ];
                }

                // Fetch subjects with school-specific filters
                $subjectsQuery = Planner::select('class_id', 'subject_id')
                    ->with('subject')->distinct()
                    ->where('class_id', $classId)
                    ->when($userBoard != 0, function ($query) use ($userBoard) {
                        return $query->where('board_id', $userBoard);
                    })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    })
                    ->where(function ($query) use ($schoolId, $role, $teacherAssignedClasses, $teacherAssignedSubjects, $schoolAssignedClasses, $schoolAssignedSubjects, $schoolAssignedSeries) {
                        // School-specific planners
                        $query->where('school_id', $schoolId);

                        // Or universal planners that match school's assigned subjects and series
                        if (!empty($schoolAssignedSubjects)) {
                            $query->orWhere(function ($q) use ($schoolAssignedSubjects, $schoolAssignedSeries) {
                                $q->whereNull('school_id')
                                    ->whereIn('subject_id', $schoolAssignedSubjects);

                                if (!empty($schoolAssignedSeries)) {
                                    $q->whereIn('series_id', $schoolAssignedSeries);
                                }
                            });
                        }

                        // If teacher, filter by assigned classes and subjects
                        if ($role === "school_teacher") {
                            if (!empty($teacherAssignedClasses)) {
                                $query->whereIn('class_id', $teacherAssignedClasses);
                            }
                            if (!empty($teacherAssignedSubjects)) {
                                $query->whereIn('subject_id', $teacherAssignedSubjects);
                            }
                        }
                        if ($role === "school_admin") {
                            if (!empty($schoolAssignedClasses)) {
                                $query->whereIn('class_id', $schoolAssignedClasses);
                            }
                            if (!empty($schoolAssignedSubjects)) {
                                $query->whereIn('subject_id', $schoolAssignedSubjects);
                            }
                        }
                    });

                $subjects = $subjectsQuery->get();

                return view('schoolPortal.planner.index', compact('plannerType', 'schoolId', 'classes', 'weeks', 'weekWiseData', 'subjects'));
            } elseif ($plannerType == 'monthly') {

                $monthName = $request->query('month');
                if ($monthName) {
                    $monthNum = date('m', strtotime($monthName));
                    $startOfMonth = now()->setMonth($monthNum)->startOfMonth()->format('Y-m-d');
                    $endOfMonth   = now()->setMonth($monthNum)->endOfMonth()->format('Y-m-d');
                } else {
                    $startOfMonth = now()->startOfMonth()->format('Y-m-d');
                    $endOfMonth   = now()->endOfMonth()->format('Y-m-d');
                }
                // dd($assignedSeriesClassSubjects);
                // Fetch all classes with monthly planners with school-specific filters
                $classesWithMonthlyPlannersQuery = Planner::with(['class', 'subject', 'chapter'])
                    ->when($userBoard != 0, function ($query) use ($userBoard) {
                        return $query->where('board_id', $userBoard);
                    })
                    ->when($userMedium != 0, function ($query) use ($userMedium) {
                        return $query->where('medium_id', $userMedium);
                    })
                    ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                        $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                            ->orWhereBetween('completion_date', [$startOfMonth, $endOfMonth])
                            ->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('start_date', '<', $startOfMonth)
                                    ->where('completion_date', '>', $endOfMonth);
                            });
                    })
                    ->where('type', 'monthly')

                    ->when($role === "school_admin", function ($query) use ($assignedSeriesClassSubjects) {
                        $query->where(function ($subQuery) use ($assignedSeriesClassSubjects) {
                            foreach ($assignedSeriesClassSubjects as $assigned) {
                                $subQuery->orWhere(function ($inner) use ($assigned) {
                                    $inner->where('class_id', $assigned['class_id'])
                                        ->where('series_id', $assigned['series_id'])
                                        ->where('subject_id', $assigned['subject_id']);
                                });
                            }
                        });
                    })
                    ->when($role === "school_teacher", function ($query) use ($teacherAssignedClasses) {
                        $query->whereIn('class_id', $teacherAssignedClasses);
                    });


                $classesWithMonthlyPlanners = $classesWithMonthlyPlannersQuery->get();

                // Group the data by class
                $classPlannerData = [];
                foreach ($classesWithMonthlyPlanners as $planner) {
                    $classId     = $planner->class_id;
                    $subjectName = $planner->subject->name ?? 'No Subject';
                    $chapterIds  = explode(',', $planner->chapter_id);                                                // Convert string to array
                    $chapters    = CourseChapter::whereIn('id', $chapterIds)->pluck('chapter_name', 'id')->toArray(); // Get all chapter names

                    // Structure the data for display
                    $classPlannerData[$classId][] = [
                        'subject'         => $subjectName,
                        'chapter_id'      => $chapterIds, // Store array of IDs
                        'titles'          => $chapters,   // Store an array of chapter names
                        'planner_id'      => $planner->id,
                        'start_date'      => $planner->start_date,
                        'completion_date' => $planner->completion_date,
                    ];
                }
                return view('schoolPortal.planner.monthly_planner', compact('classes', 'classPlannerData', 'startOfMonth', 'endOfMonth'));
            }
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function markHoliday(Request $request)
    {
        // Get the holiday date, school ID, and day index from the request
        $holidayDate = $request->get('holiday_date');
        $schoolId    = $request->get('school_id');
        $dayIndex    = $request->get('day_index');

        // Step 2: Get all planner rows where school_id is null (universal planner)
        $planners = Planner::whereNull('school_id')->get();
        // Step 3: Create new planner rows for each `school_id`
        foreach ($planners as $planner) {
            // Adjust the planner's start and end dates to exclude the holiday
            if ($planner->start_date <= $holidayDate && $planner->completion_date >= $holidayDate) {
                // Calculate new start and end dates after skipping the holiday
                $newStartDate = Carbon::parse($planner->start_date)->addDay()->format('Y-m-d');
                $newEndDate   = Carbon::parse($planner->completion_date)->addDay()->format('Y-m-d');

                // Create a new planner record for the specific school
                $newPlanner                  = new Planner();
                $newPlanner->school_id       = $schoolId; // Assign the new school_id
                $newPlanner->board_id        = $planner->board_id;
                $newPlanner->medium_id       = $planner->medium_id;
                $newPlanner->series_id       = $planner->series_id;
                $newPlanner->class_id        = $planner->class_id;
                $newPlanner->subject_id      = $planner->subject_id;
                $newPlanner->chapter_id      = $planner->chapter_id;
                $newPlanner->allotted_days   = $planner->allotted_days;
                $newPlanner->start_date      = $newStartDate;
                $newPlanner->completion_date = $newEndDate;
                $newPlanner->total_periods   = $planner->total_periods;

                // Save the new planner row
                $newPlanner->save();

                $plannerOff             = new PlannerOff();
                $plannerOff->planner_id = $newPlanner->id; // Reference the new planner ID
                $plannerOff->date       = $holidayDate;
                $plannerOff->save();
            }
        }

        // Step 4: Return a response indicating success
        return response()->json(['success' => true]);
    }
    public function chapterDetails($id)
    {
        $this->data['role']                    = getUserRoles();
        $plannerLesson = Planner::whereRaw("FIND_IN_SET(?, chapter_id)", [$id])->with('details', 'class', 'subject', 'board', 'medium', 'series')->first();

        if (! $plannerLesson) {
            return back()->with('error', '⚠️ Oops! The requested Chapter Planner details could not be found. Please try again. 😊');
        }
        $groupedDetails               = $plannerLesson->details->groupBy('type');
        $this->data['groupedDetails'] = $groupedDetails;
        $this->data['plannerLesson']  = $plannerLesson;
        $this->data['digitalContent'] = CourseChapter::with('chapters', 'folder', 'documents')->where('id', $id)->first();
        foreach ($this->data['digitalContent']->chapters as $chapter) {
            if (in_array($chapter->file_extension, ['mp4', 'avi', 'mov', 'm4v', 'm4p', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'm2v', 'wmv', 'flv', 'mkv', 'webm', '3gp', '3gp', 'm2ts', 'ogv', 'ts', 'mxf'])) {
                $chapter->signed_url = FileController::sendOnSignedRoute($chapter->attachment_file);
            }
        }

        // $this->data['supportingFiles'] = MediaFiles::where('tbl_id', $this->data['digitalContent']->supporting_folder_id)->where('type', 'content_upload')->get();

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

        $percentagePerDay = $daysWithoutSundays > 0 ? 100 / $daysWithoutSundays : 0;
        $today            = Carbon::today();

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

        // this is the percentage that we are calculating form the data that is done by the teacher or the school admin 
        $role                    = getUserRoles();
        $schoolId                = Auth::id();
        if ($role === "school_teacher") {
            $schoolId                = Auth::user()->userAdditionalDetail->school_id;
        }
        $isCompleted = SchoolCompletedPlanner::where('planner_id', $plannerLesson->id)->where('school_id', $schoolId)->first();
        if ($isCompleted) {
            $estimatedPercentage = 100;
        } else {
            $estimatedPercentage = 0;
        }

        $this->data['estimatedPercentage'] = $estimatedPercentage;
        $this->data['actualPercentage'] = $actualPercentage;
        $this->data['percentagePerDay'] = $percentagePerDay;
        $this->data['startDate']        = $startDate;
        $this->data['completionDate']   = $completionDate;
        return view('schoolPortal.planner.chapter_details', $this->data);
    }

    public function folderDocument($id)
    {
        $this->data['folder']     = MediaFolder::where('parent_id', Auth::id())->where('id', $id)->first();
        $this->data['folderData'] = MediaFiles::where('tbl_id', $id)->where('type', 'content_upload')->get();
        // return $this->data['folderData'];
        return view('schoolPortal.myCourses.planner_folder_details', $this->data);
    }
    public function downloadSupportingDocuments($id)
    {
        $supportingFiles = MediaFiles::where('tbl_id', $id)->where('type', 'content_upload')->get();

        if ($supportingFiles->isEmpty()) {
            return redirect()->back()->with('error', 'No documents available for download.');
        }

        $zipFileName = 'supporting-documents.zip';
        $zip         = new ZipArchive;
        $zipPath     = storage_path('app/' . $zipFileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($supportingFiles as $file) {
                $filePath = storage_path('app/public/uploads/media-files/' . $file->attachment_file);
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

    public function plannerVisibilty(Request $request)
    {

        foreach ($request->classes as $class) {
            if ($class['type'] != null) {
                $res = SchoolPlannerVisibility::updateOrCreate(['school_id' => Auth::id(), 'class_id' => $class['id']], ['type' => $class['type']]);
            }
        }
        if ($res) {
            return redirect()->route('daily.planner')->with(['success' => config('constants.FLASH_REC_UPDATE_1')]);
        }
        return redirect()->back()->with(['error' => config('constants.FLASH_REC_UPDATE_0')]);
    }
    public function confirmPlannerComplete(Request $request, $id)
    {
        $role                    = getUserRoles();
        $schoolId                = Auth::id();
        if ($role === "school_teacher") {
            $schoolId                = Auth::user()->userAdditionalDetail->school_id;
        }

        $res = SchoolCompletedPlanner::create([
            'name' => $request->name,
            'is_active' => $request->is_active,
            'planner_id' => $id,
            'school_id' => $schoolId,
            'completed_by' => Auth::id(),
            'user_type' => $role,
            'completion_date' => now()->toDateString(),
        ]);
        if ($res) {
            return redirect()->route('daily.planner')->with(['success' => config('constants.FLASH_REC_UPDATE_1')]);
        }
        return redirect()->back()->with(['error' => config('constants.FLASH_REC_UPDATE_0')]);
    }
}
