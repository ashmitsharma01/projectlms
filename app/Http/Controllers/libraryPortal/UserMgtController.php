<?php

namespace App\Http\Controllers\libraryPortal;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Payment;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserMgtController extends Controller
{
    public $data = [];

    public function studentManager(Request $request)
    {
        $library_id               = Library::where('user_id', Auth::id())->value('id');
        $query = Student::where('library_id', $library_id)->with(['user', 'userRole']);
        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }
        if ($request->filled('sort')) {
            $sortOrder = $request->sort === 'asc' ? 'ASC' : 'DESC';
            $query->orderBy('name', $sortOrder);
        } else {
            $query->orderBy('created_at', 'DESC'); // Default sorting
        }

        $this->data['students'] = $query->get();

        return view('libraryPortal.user.student_manager', $this->data);
    }

    public function studentAdd(Request $request)
    {
        $this->data['libraries'] = Library::pluck('name', 'id');
        return view('libraryPortal.user.student-add-edit', $this->data);
    }

    public function userSave(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();

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
                    'name'            => $request->name,
                    'email'           => $request->email,
                    'mobile_no'       => $request->mobile_no,
                    'password'        => Hash::make('Lms@1234'),
                    'vallidate_string' => 'Lms@1234',
                ]
            );

            UserRole::updateOrCreate(
                ['user_id' => $user->id],
                ['role_slug' => 'student']
            );

            if (!$request->id) {
                $library     = Library::where('user_id', Auth::id())->first();
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
                $library     = Library::where('user_id', Auth::id())->first();
                $admissionNo = Student::where('user_id', $request->id)->value('admission_no');
            }

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'library_id'   => $library->id,
                    'name'         => $request->name,
                    'admission_no' => $admissionNo,
                    'joining_date' => $request->joining_date,
                    'address'      => $request->address,
                    'pincode'      => $request->pincode,
                    'status'       => $request->status
                ]
            );

            DB::commit();

            return redirect()->route('student.manager')
                ->with('success', $request->id ? 'Student updated' : 'Student created');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function lastPayment(Request $request)
    {
        $payment = Payment::where('student_user_id', $request->user_id)
            ->latest()
            ->first();

        return response()->json($payment);
    }
}
