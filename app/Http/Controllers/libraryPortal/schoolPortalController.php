<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\MediaFiles;
use App\Models\MediaFolder;
use App\Models\SchoolClass;
use App\Models\State;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolPortalController extends Controller
{
    public $data = [];
    public function dashboard(Request $request)
    {
        $this->data['students'] = User::where('created_by', Auth::id())->with(['userAdditionalDetail'])
            ->whereHas('userAdditionalDetail', function ($query) {
                $query->where('role', 'user');
            })->count();
        $this->data['teachers'] = User::where('created_by', Auth::id())->with('userAdditionalDetail')
            ->whereHas('userAdditionalDetail', function ($query) {
                $query->where('role', 'teacher');
            })->count();
        $this->data['classes'] = SchoolClass::all();

        return view('schoolPortal.dashboard', $this->data);
    }
    public function onlineClass(Request $request)
    {
        return view('schoolPortal.online_class');
    }
    public function classBooks(Request $request)
    {
        return view('schoolPortal.class_books');
    }
   
    public function classSubjectPlanner(Request $request)
    {
        return view('schoolPortal.class_subject_planner');
    }
  
    public function lessonPlanner(Request $request)
    {
        return view('schoolPortal.lesson_planner');
    }

    public function lessonView(Request $request)
    {
        return view('schoolPortal.lesson_view');
    }

    public function onlineClassDetails(Request $request)
    {
        return view('schoolPortal.online_class_details');
    }

    public function studentManager()
    {
        $this->data['classes'] = SchoolClass::pluck('name', 'id');
        $this->data['students'] = User::where('created_by', Auth::id())->with(['userAdditionalDetail', 'studentDetails'])
            ->whereHas('userAdditionalDetail', function ($query) {
                $query->where('role', 'user');
            })->orderBy('created_at', 'DESC')->paginate(10);
        // dd($this->data['students']);
        return view('schoolPortal.student_manager', $this->data);
    }

    public function teacherManager(Request $request)
    {
        $this->data['subjects'] = Subject::pluck('name', 'id');
        $this->data['classes'] = SchoolClass::pluck('name', 'id');
        $this->data['cities'] = City::all();
        $this->data['states'] = State::pluck('name', 'id');
        $this->data['teachers'] = User::where('created_by', Auth::id())->with('userAdditionalDetail')
            ->whereHas('userAdditionalDetail', function ($query) {
                $query->where('role', 'teacher');
            })->orderBy('created_at', 'DESC')->paginate(10);
        // dd($this->data['teachers']);
        return view('schoolPortal.teacher_manager', $this->data);
    }

    public function editTeacher($id)
    {
        $this->data['data'] = User::where('id', $id)->with('userAdditionalDetail')->first();
        return view('schoolPortal.teacher_manager', $this->data);
    }
}
