<?php

namespace App\Http\Controllers\libraryPortal;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SeatAssignment;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeatMgtController extends Controller
{
    public $data = [];

    public function index(Request $request)
    {
        $library_id               = Auth::id();
        $query = SeatAssignment::where('library_id', $library_id)->with(['user', 'seat']);
        // if ($request->filled('status')) {
        //     $query->where('status', 'like', '%' . $request->status . '%');
        // }
        if ($request->filled('sort')) {
            $sortOrder = $request->sort === 'asc' ? 'ASC' : 'DESC';
            $query->orderBy('name', $sortOrder);
        } else {
            $query->orderBy('created_at', 'DESC'); // Default sorting
        }

        $this->data['seats'] = $query->get();
        $this->data['totalSeats'] = Library::where('user_id',Auth::id())->value('total_seats');
        return view('libraryPortal.seats.index', $this->data);
    }

    public function seatAdd(Request $request)
    {
        return view('libraryPortal.seats.seat-add-edit', $this->data);
    }

    public function userSave(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => ['nullable', 'email', Rule::unique('users', 'email')->ignore($request->id)],
                'mobile_no'    => ['required', 'digits:10', Rule::unique('users', 'mobile_no')->ignore($request->id)],
                'address'      => 'required|string',
                'pincode'      => 'required|string|max:15',
                'joining_date' => 'required|date',
                'status'       => 'required|in:0,1',
            ]);

            $user = User::updateOrCreate(
                ['id' => $request->id],
                [
                    'name'             => $request->name,
                    'email'            => $request->email,
                    'mobile_no'        => $request->mobile_no,
                    'password'         => Hash::make('Lms@1234'),
                    'validate_string'  => 'Lms@1234',
                ]
            );

            UserRole::updateOrCreate(
                ['user_id' => $user->id],
                ['role_slug' => 'student']
            );

            if (!$request->id) {
                $library     = Library::where('user_id',Auth::id())->first();
                $libraryCode = $library->library_code;

                $lastStudent = Student::where('library_id', $library->id)
                    ->where('admission_no', 'LIKE', $libraryCode . '-%')
                    ->orderBy('id', 'DESC')
                    ->first();

                $newNumber = $lastStudent
                    ? ((int) substr($lastStudent->admission_no, strlen($libraryCode) + 1)) + 1
                    : 1;

                $admissionNo = $libraryCode . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $admissionNo = Student::where('user_id', $request->id)->value('admission_no');
            }

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'library_id'   => Auth::id(),
                    'name'         => $request->name,
                    'admission_no' => $admissionNo,
                    'joining_date' => $request->joining_date,
                    'address'      => $request->address,
                    'pincode'      => $request->pincode,
                    'status'       => $request->status
                ]
            );

            return redirect()->route('student.manager')
                ->with('success', $request->id ? 'Student updated' : 'Student created');
        } catch (ValidationException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function studentEdit($id)
    {
        try {
            $user = Student::with(['user'])->findOrFail($id);

            $this->data['heading'] = "Edit";
            $this->data['studentData'] = $user;
            $this->data['libraries'] = Library::pluck('name', 'id');

            return view('libraryPortal.user.student-add-edit', $this->data);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function studentDelete($id)
    {
        try {
            $userDeleted = User::where('id', $id)->delete();
            $studentDeleted = Student::where('user_id', $id)->delete();
            $roleDeleted = UserRole::where('user_id', $id)->delete();

            if (!$userDeleted || !$studentDeleted || !$roleDeleted) {
                return redirect()->back()->with('error', 'Student Not Deleted');
            }

            return redirect()->back()->with('success', 'Student Deleted');
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    // public function studentEdit($id)
    // {
    //     $role                   = getUserRoles();
    //     $parentId               = Auth::id();
    //     $teacherAssignedClasses = [];
    //     if ($role == "school_teacher") {
    //         $parentId = Auth::user()->userAdditionalDetail->school_id;

    //         // Get the teacher's assigned classes
    //         $teacherAssignedClasses = getTeacherAssignedClasses();
    //     }
    //     $this->data['classes']        = getUserSchoolClasses($parentId);
    //     $this->data['sections'] = Section::where('is_active', 1)->pluck('section_name', 'id');

    //     $this->data['teacherClasses'] = SchoolClass::whereIn('id', $teacherAssignedClasses)->pluck('name', 'id');

    //     $this->data['roles'] = Role::get();
    //     $this->data['studentData']    = User::with('userAdditionalDetail')->find($id);

    //     return view('schoolPortal.user.student-add-edit', $this->data);
    // }

    // public function UnVerfiredStudent(Request $request)
    // {
    //     $role                   = getUserRoles();
    //     $parentId               = Auth::id();

    //     $this->data['classes']        = getUserSchoolClasses($parentId);

    //     $query = User::where('is_verified', 0)->with(['userAdditionalDetail', 'studentDetails', 'userAccessCode'])
    //         ->whereHas('userAdditionalDetail', function ($query) use ($parentId) {
    //             $query->where('role', 'school_student')
    //                 ->where('school_id', $parentId);
    //         });

    //     $this->data['students'] = $query->paginate(config('constants.PAGINATION.default'));
    //     $this->data['roles'] = Role::get();
    //     return view('schoolPortal.user.unverfied-students', $this->data);
    // }

    // public function toggleStatus($id)
    // {
    //     try {
    //         $action_date = \Carbon\Carbon::now()->format('Y-m-d');
    //         $user        = User::where('id', $id)->first();
    //         if ($user) {
    //             $userJson = $user->toJson();
    //             $userlog  = UserLog::create([
    //                 'user_id'     => $user->id,
    //                 'updated_by'  => Auth::id(),
    //                 'title'       => ($user->status == 1) ? 'User Inactived' : 'User Actived',
    //                 'uri'         => '--',
    //                 'action_as'   => ($user->status == 1) ? 'user_inactive' : 'user_active',
    //                 'action_date' => $action_date,
    //                 'json_data'   => $userJson,

    //                 'log_type'    => 'user_update',
    //                 'log_date'    => now(),
    //             ]);
    //             $user->status = ($user->status == 1) ? 0 : 1;
    //             $user->save(['status']);
    //             if ($userlog) {
    //                 return response()->json(['status' => 'success']);
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error'], 500);
    //     }
    // }

    // public function getUserLogs($userId)
    // {
    //     $logs = UserLog::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    //     return response()->json($logs);
    // }



    // public function userSendmail($user)
    // {
    //     if ($user) {
    //         $user = User::find($user->id);
    //         $templateId = 30;
    //         $data = [
    //             'NAME' => $user->name,
    //             'EMAIL' => $user->email,
    //             'MOBILE_NUMBER' => $user->mobile_no,
    //             'USERNAME' => $user->username,
    //             'PASSWORD' => $user->vallidate_string,
    //         ];
    //         if ($user) {
    //             sendEmail($templateId, $user->email, $data);
    //         }
    //     }
    // }
    // public function UnVerfiredStudentSave(Request $request)
    // {
    //     $role     = getUserRoles();
    //     $parentId = Auth::id();

    //     // If the role is "school_teacher", use school_id from UserAdditionalDetail
    //     if ($role == "school_teacher") {
    //         $parentId = Auth::user()->userAdditionalDetail->school_id;
    //     }
    //     // dd($request->all());
    //     if ($request->id > 0) {
    //         $success = config('constants.FLASH_REC_UPDATE_1');
    //         $error   = config('constants.FLASH_REC_UPDATE_0');
    //     } else {
    //         $success = config('constants.FLASH_REC_ADD_1');
    //         $error   = config('constants.FLASH_REC_ADD_0');
    //     }

    //     $role          = $request->input('role');
    //     $role_selected = $role ?? $request->selectedRole;
    //     switch ($role_selected) {
    //         case 'school_student':
    //             $request->validate([
    //                 'name'             => 'required',
    //                 'admission_no'     => 'required',
    //                 'parent_mobile_no' => [
    //                     'required',
    //                     'numeric',
    //                     'regex:/^\d{10}$/',
    //                     'unique:users,mobile_no,' . $request->id,
    //                 ],
    //                 'email'            => [
    //                     'nullable',
    //                     'email',
    //                     'unique:users,email,' . $request->id,
    //                     'regex:/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/',
    //                 ],
    //                 'class'            => 'required',
    //                 'parent_name'      => 'required',
    //                 'admission_date'   => 'required',
    //                 'dob'              => 'required',
    //             ]);
    //             $user = User::updateOrCreate(
    //                 [
    //                     'id' => $request->id,
    //                 ],
    //                 [
    //                     'name'             => $request->name,
    //                     'email'            => $request->email ?? null,
    //                     'mobile_no'        => $request->parent_mobile_no,
    //                     'password'         => Hash::make('Mitt@123'),
    //                     'validate_string' => 'Mitt@123',
    //                     'created_by'       => Auth::id(),
    //                     'is_verified'       => 1,
    //                 ]
    //             );

    //             if (! $user) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }

    //             $userrole = UserRole::updateOrCreate(
    //                 ['user_id' => $user->id],
    //                 ['role_slug' => $role_selected]
    //             );

    //             if (! $userrole) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }

    //             // $admissionDate = Carbon::hasFormat($request->admission_date, 'm/d/Y') ? Carbon::createFromFormat('m/d/Y', $request->admission_date)->format('Y-m-d') : $request->admission_date;
    //             // $dob = Carbon::hasFormat($request->dob, 'm/d/Y') ? Carbon::createFromFormat('m/d/Y', $request->dob)->format('Y-m-d') : $request->dob;

    //             $admissionDate = Carbon::parse($request->admission_date)->format('Y-m-d');
    //             $dob           = Carbon::parse($request->dob)->format('Y-m-d');

    //             $studentdetail = StudentDetails::updateOrCreate(
    //                 [
    //                     'user_id' => $user->id,
    //                 ],
    //                 [
    //                     'user_id'     => $user->id,
    //                     'parent_id'   => $parentId,
    //                     'school_id'   => $parentId,
    //                     'doj'         => $admissionDate,
    //                     'dob'         => $dob,
    //                     'class'       => $request->class,
    //                     'parent_name' => $request->parent_name ?? null,
    //                     'section'     => $request->section ?? null,
    //                 ]
    //             );

    //             if (! $studentdetail) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }

    //             $user_addtional_detail = UserAdditionalDetail::updateOrCreate(
    //                 [
    //                     'user_id' => $user->id,
    //                 ],
    //                 [
    //                     'role'         => $role_selected,
    //                     'school_id'    => $parentId,
    //                     'user_id'      => $user->id,
    //                     'admission_no' => $request->admission_no,
    //                 ]
    //             );

    //             if (! $user_addtional_detail) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }

    //             return redirect()->back()->with(['success' => $success]);

    //         case 'school_teacher':
    //             $request->validate([
    //                 'name'          => 'required',
    //                 // 'last_name'     => 'required',
    //                 'gender'        => 'required',
    //                 'mobile_no'     => [
    //                     'required',
    //                     'numeric',
    //                     'regex:/^\d{10}$/',
    //                     'unique:users,mobile_no,' . $request->id,
    //                 ],
    //                 'email'         => [
    //                     'required',
    //                     'email',
    //                     'unique:users,email,' . $request->id,
    //                     'regex:/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/',
    //                 ],
    //                 'address'       => 'required',
    //                 'city'          => 'required',
    //                 'state'         => 'required',
    //                 // 'country'       => 'required',
    //                 'qualification' => 'required',
    //                 'experience'    => 'required',
    //                 'age'           => 'required|numeric',
    //             ]);

    //             if ($request->subject || $request->class) {
    //                 $request->validate([
    //                     'subject' => 'required',
    //                     'class'   => 'required',
    //                     'dob'     => 'required|date',
    //                 ]);
    //             } else {
    //                 $request->validate([
    //                     'classes_assigned' => 'required|boolean',
    //                 ]);
    //             }

    //             $user = User::updateOrCreate(
    //                 [
    //                     'id' => $request->id,
    //                 ],
    //                 [
    //                     'name'             => $request->name,
    //                     'mobile_no'        => $request->mobile_no,
    //                     'email'            => $request->email,
    //                     'password'         => Hash::make('Mitt@123'),
    //                     'validate_string' => 'Mitt@123',
    //                     'created_by'       => $parentId,
    //                 ]
    //             );

    //             if (! $user) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }

    //             $userrole = UserRole::updateOrCreate(
    //                 ['user_id' => $user->id],
    //                 ['role_slug' => $role_selected]
    //             );

    //             if (! $userrole) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }

    //             if ($request->dob) {
    //                 // $dob = Carbon::createFromFormat('m/d/Y', $request->dob)->format('Y-m-d');
    //                 $dob = Carbon::parse($request->dob)->format('Y-m-d');
    //             }

    //             $user_addtional_detail = UserAdditionalDetail::updateOrCreate(
    //                 [
    //                     'user_id' => $user->id,
    //                 ],
    //                 [
    //                     'role'              => $role_selected,
    //                     'school_id'         => $parentId,
    //                     'user_id'           => $user->id,
    //                     // 'last_name'         => $request->last_name,
    //                     'gender'            => $request->gender,
    //                     'age'               => $request->age,
    //                     'address'           => $request->address,
    //                     'city'              => $request->city,
    //                     'state'             => $request->state,
    //                     // 'country'           => $request->country,
    //                     'qualification'     => $request->qualification,
    //                     'class_assigned'    => $request->classes_assigned,
    //                     'experience'        => $request->experience,
    //                     'dob'               => $dob ?? null,
    //                     'assigned_classes'  => $request->class ? implode(',', $request->class) : 'null',
    //                     'assigned_subjects' => $request->subject ? implode(',', $request->subject) : 'null',
    //                 ]
    //             );

    //             if (! $user_addtional_detail) {
    //                 return redirect()->back()->with(['error' => config('constants.API_MSG.REC_ADD_FAILED')]);
    //             }
    //             return redirect()->back()->with(['success' => $success]);

    //         default:
    //             return redirect()->back()->with(['error' => $error]);
    //     }
    //     // return redirect()->back()->with(['error' => $error]);

    // }
    // public function getCities($state)
    // {
    //     $cities = City::where('state_id', $state)->pluck('city', 'id');
    //     return response()->json($cities);
    // }

    // public function exportStudents()
    // {
    //     $role                   = getUserRoles();
    //     $parentId               = Auth::id();
    //     $teacherAssignedClasses = [];
    //     if ($role === "school_teacher") {
    //         $parentId               = Auth::user()->userAdditionalDetail->school_id;
    //         $teacherAssignedClasses = getTeacherAssignedClasses();
    //     }
    //     $export      = new StudentsExport($role, $parentId, $teacherAssignedClasses);
    //     $fileContent = \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
    //     return response($fileContent, 200, [
    //         'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //         'Content-Disposition' => 'attachment; filename="students_list.xlsx"',
    //     ]);
    // }


}
