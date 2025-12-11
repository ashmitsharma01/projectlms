<?php

namespace App\Http\Controllers\libraryPortal;

use App\Exports\ActiveAccessCodesExport;
use App\Http\Controllers\Controller;
use App\Models\AccessCode;
use App\Models\AccessCodeEmbibe;
use App\Models\Course;
use App\Models\CourseChapter;
use App\Models\MediaFiles;
use App\Models\OnlineClass;
use App\Models\Planner;
use App\Models\PlannerOff;
use App\Models\SchoolAssignedClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserManual;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public $data = [];
    public function dashboard(Request $request)
    {
        $this->data['name'] = 'My library';

        return view('libraryPortal.dashboard', $this->data);
    }


    private function calculateChangePercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }
    public function activeAccessCodeDownload()
    {
        $file = Excel::raw(new ActiveAccessCodesExport, \Maatwebsite\Excel\Excel::XLSX);
        return Response::make($file, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="active_access_codes.xlsx"',
        ]);
    }

    public function getClassWiseStudentCountChartData()
    {
        $parentId = Auth::user()->userAdditionalDetail->school_id;
        $teacherAssignedClasses = getTeacherAssignedClasses();

        $studentCountsByClass = DB::table('student_details')
            ->select('student_details.class', 'classes.name as class_name', DB::raw('COUNT(*) as count'))
            ->join('user_additional_details', 'student_details.user_id', '=', 'user_additional_details.user_id')  // Join with user_additional_details
            ->join('classes', 'student_details.class', '=', 'classes.id')
            ->where('user_additional_details.school_id', $parentId)
            ->whereIn('student_details.class', $teacherAssignedClasses)
            ->groupBy('student_details.class', 'classes.name')
            ->get();

        $chartData = $studentCountsByClass->map(function ($studentCount) {
            $color = in_array($studentCount->class, getTeacherAssignedClasses()) ? '#61F51D' : '#EC7172';
            return [
                'name' => $studentCount->class_name,
                'y' => (int)$studentCount->count,
                'color' => $color,
            ];
        });

        return $chartData->toArray();
    }
    public function downloadApp(Request $request)
    {
        $this->data['setting'] = Setting::pluck('field_value', 'field_name')->toArray();
        return view('schoolPortal.download-app', $this->data);
    }
    public function userManual(Request $request)
    {
        $userRole = getUserRoles(); // e.g., 'admin'

        $this->data['manuals'] = UserManual::where('is_active', 1)->whereRaw("FIND_IN_SET(?, visible_to_roles)", [$userRole])->get();
        return view('schoolPortal.user-manual', $this->data);
    }
}
