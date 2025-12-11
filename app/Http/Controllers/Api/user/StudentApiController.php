<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Api\BaseController;
use App\Models\Library;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentApiController extends BaseController
{
    public $data     = [];
    public $res      = [];


    public function studentSave(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'address'  => 'required|string',
                'email'      => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($request->id)
                ],
                'mobile_no'  => [
                    'required',
                    'min:10',
                    'max:10',
                    Rule::unique('users', 'mobile_no')->ignore($request->id)
                ],
                'pincode'  => 'required|string|max:15',
                'status'   => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                    'data'    => null
                ], 422);
            }

            // Create or update user
            $user = User::updateOrCreate(
                ['id' => $request->id],
                [
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'mobile_no' => $request->mobile_no,
                    'password' => $request->password
                        ? Hash::make($request->password)
                        : Hash::make('Lms@1234'),
                    'vallidate_string' => $request->password ?? 'Lms@1234',
                ]
            );

            // Assign role
            UserRole::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_slug' => 'student'
                ]
            );

            // Generate library-specific admission number for new student
            if (!$request->id) {
                $library = Library::find($request->library_id);
                $libraryCode = $library->library_code;

                $lastStudent = Student::where('library_id', $library->id)
                    ->where('admission_no', 'LIKE', $libraryCode . '-%')
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($lastStudent) {
                    $lastNumber = (int) substr($lastStudent->admission_no, strlen($libraryCode) + 1);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $admissionNo = $libraryCode . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $admissionNo = $request->admission_no; // keep existing on update
            }

            // Create or update student
            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'library_id'   => $request->library_id,
                    'user_id'      => $user->id,
                    'name'         => $request->name,
                    'admission_no' => $admissionNo,
                    'joining_date' => $request->joining_date,
                    'address'      => $request->address,
                    'pincode'      => $request->pincode,
                    'status'       => $request->status
                ]
            );

            return response()->json([
                'status'  => true,
                'message' => $request->id ? 'Student updated successfully.' : 'Student created successfully.',
                'data'    => $student
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }


    public function getStudents(Request $request)
    {
        try {

            $students = Student::with(['user', 'userRole'])->get();

            return response()->json([
                'status'  => true,
                'message' =>'Student fetched successfully.',
                'data'    => $students
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }
}
