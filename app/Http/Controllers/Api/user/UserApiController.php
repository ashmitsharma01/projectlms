<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Api\BaseController;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserApiController extends BaseController
{
    public $data     = [];
    public $res      = [];


    public function userCreate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'email'     => 'nullable|email|unique:users,email,' . $request->id,
                'mobile_no'     => 'required|min:10|max:10|unique:users,mobile_no,' . $request->id,
                'password'  => $request->id ? 'nullable|min:8' : 'required|min:8',
                'role'      => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                    'data'    => null
                ], 422);
            }

            // Create / Update User
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

            // Find the role slug
            $roleSlug = Role::where('id', $request->role)->value('role_slug');

            // Create/Update user role mapping
            UserRole::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_slug' => $roleSlug
                ]
            );

            return response()->json([
                'status'  => true,
                'message' => $request->id ? 'User updated successfully.' : 'User created successfully.',
                'data'    => $user
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}
