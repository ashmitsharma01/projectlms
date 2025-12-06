<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Api\BaseController;
use App\Models\Library;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class LibraryApiController extends BaseController
{
    public $data     = [];
    public $res      = [];


    public function libraryCreate(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'address'  => 'required|string',
                'city'     => 'required|string|max:100',
                'email'     => 'nullable|email|unique:users,email,' . $request->id,
                'mobile_no'     => 'required|min:10|max:10|unique:users,mobile_no,' . $request->id,
                'state'    => 'required|string|max:100',
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
            $user = User::updateOrCreate(
                ['id' => $request->id],
                [
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'mobile_no'    => $request->mobile_no,
                    'password' => $request->password
                        ? Hash::make($request->password)
                        : Hash::make('Lms@1234'),
                    'vallidate_string' => $request->password ?? 'Lms@1234',
                ]
            );

            UserRole::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_slug' => 'library'
                ]
            );


            $library = Library::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'admin_id' => 1, // we can use this to save the id/name of the man who is managing the library
                    'user_id' => $user->id,
                    'name'     => $request->name,
                    'address'  => $request->address,
                    'city'     => $request->city,
                    'state'    => $request->state,
                    'pincode'  => $request->pincode,
                    'status'   => $request->status
                ]
            );

            return response()->json([
                'status'  => true,
                'message' => $request->id ? 'Library updated successfully.' : 'Library created successfully.',
                'data'    => $library
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }

    public function getLibrary(Request $request)
    {
        try {

            $libraries = Library::with(['user', 'userRole'])->get();

            return response()->json([
                'status'  => true,
                'message' => $request->id ? 'Library updated successfully.' : 'Library created successfully.',
                'data'    => $libraries
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
