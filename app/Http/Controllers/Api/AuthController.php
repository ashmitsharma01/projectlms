<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use App\Models\OtpSession;
use App\Models\Role;
use App\Models\Schools;
use App\Models\Setting;
use App\Models\StudentDetails;
use App\Models\User;
use App\Models\UserAdditionalDetail;
use App\Models\UserRole;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    // public function register(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'name'           => 'required|string',
    //             'mobile_no'      => 'required|min:10|max:10|unique:users,mobile_no',
    //             'email'          => 'nullable|regex:/(.+)@(.+)\.(.+)/i|unique:users,email',
    //             'user_type'      => 'required',
    //             'password'       => 'required|min:8|confirmed',
    //             'terms_accepted' => 'required|accepted',
    //         ]);

    //         if ($validator->fails()) {
    //             // Get first validation error message only
    //             $firstError = collect($validator->errors()->all())->first();

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => $firstError,
    //                 'data'    => null,
    //             ], 422);
    //         }

    //         $existingUser = User::where('mobile_no', $request->mobile_no)->where('status', 1)->first();

    //         if ($existingUser) {
    //             return $this->handleExistingUser($existingUser);
    //         }
    //         if ($request->filled('guest_user_id')) {
    //             $data = User::find($request->guest_user_id);

    //             if (!$data) {
    //                 return ApiResponseService::error(404, 'Guest user not found.');
    //             }
    //         } else {
    //             $data = new User;
    //         }

    //         $data->name = $request->name;
    //         $data->mobile_no = $request->mobile_no;
    //         $data->user_type = $request->user_type;
    //         $data->email = $request->email;
    //         $data->password = Hash::make($request->password);
    //         $data->validate_string = $request->password;
    //         $data->is_verified = 0;
    //         $data->source = 'register';

    //         $data->save();

    //         $selectedUserType = $request->input('user_type');
    //         $newData = $request->input('school_name');


    //         if ($selectedUserType == 'school_admin' && $newData) {
    //             $roleSlug = Role::where('role_slug', 'b2c_student')->first();
    //             $schoolSelected = Schools::where('name', $newData)->first();
    //             if ($schoolSelected) {
    //                 return $this->sendError(config('constants.API_MSG.ALREADY_ACCOUNT_ERROR'), 422);
    //             } else {
    //                 $request->validate([
    //                     'school_name' => 'required|string|unique:schools,name'
    //                 ]);
    //                 $SchoolUserData = new User;
    //                 $SchoolUserData->name = $newData;
    //                 $SchoolUserData->is_verified = 0;
    //                 $SchoolUserData->save();

    //                 if (!$SchoolUserData) {
    //                     return redirect()->back()->with(['error' => 'Something went wrong']);
    //                 }

    //                 $userRole = new UserRole();
    //                 $userRole->user_id = $SchoolUserData->id;
    //                 $userRole->role_slug = 'school_admin';
    //                 $userRole->save();

    //                 $school = new Schools();
    //                 $school->user_id = $SchoolUserData->id;
    //                 $school->name = $newData;
    //                 $school->save();
    //             }

    //             UserAdditionalDetail::create(['user_id' => $data->id, 'school_id' => $school->user_id, 'role' => 'b2c_student']);
    //         } elseif ($selectedUserType == 'school_teacher'  && $newData) {
    //             $roleSlug = Role::where('role_slug', 'b2c_student')->first();
    //             $schoolSelected = Schools::where('name', $newData)->first();

    //             if ($schoolSelected) {
    //                 $schoolUserId = Schools::where('id', $schoolSelected->id)->value('user_id');
    //             } else {
    //                 $request->validate([
    //                     'school_name' => 'required|string|unique:schools,name'
    //                 ]);
    //                 $SchoolUserData = new User;
    //                 $SchoolUserData->name = $newData;
    //                 $SchoolUserData->is_verified = 0;
    //                 $SchoolUserData->save();

    //                 if (!$SchoolUserData) {
    //                     return redirect()->back()->with(['error' => 'Something went wrong']);
    //                 }

    //                 $userRole = new UserRole();
    //                 $userRole->user_id = $SchoolUserData->id;
    //                 $userRole->role_slug = 'school_admin';
    //                 $userRole->save();

    //                 $school = new Schools();
    //                 $school->user_id = $SchoolUserData->id;
    //                 $school->name = $newData;
    //                 $school->save();

    //                 $schoolUserId = $school->user_id;
    //             }
    //             UserAdditionalDetail::create(['user_id' => $data->id, 'school_id' => $schoolUserId, 'role' => 'b2c_student']);
    //         } elseif ($selectedUserType === 'school_student' && $newData) {
    //             $roleSlug = Role::where('role_slug', 'school_student')->first();
    //             $schoolSelected = Schools::where('name', $newData)->first();

    //             if ($schoolSelected) {
    //                 $schoolUserId = Schools::where('id', $schoolSelected->id)->value('user_id');
    //             } else {
    //                 $request->validate([
    //                     'school_name' => 'required|string|unique:schools,name'
    //                 ]);
    //                 $SchoolUserData = new User;
    //                 $SchoolUserData->name = $newData;
    //                 $SchoolUserData->is_verified = 0;
    //                 $SchoolUserData->save();

    //                 if (!$SchoolUserData) {
    //                     return redirect()->back()->with(['error' => 'Something went wrong']);
    //                 }

    //                 $userRole = new UserRole();
    //                 $userRole->user_id = $SchoolUserData->id;
    //                 $userRole->role_slug = 'school_admin';
    //                 $userRole->save();

    //                 $school = new Schools();
    //                 $school->user_id = $SchoolUserData->id;
    //                 $school->name = $newData;
    //                 $school->save();

    //                 $schoolUserId = $school->user_id;
    //             }
    //             StudentDetails::create(['user_id' => $data->id, 'school_id' => $schoolUserId, 'parent_id' => $schoolUserId, 'class' => $request->class_id]);
    //             UserAdditionalDetail::create(['user_id' => $data->id, 'school_id' => $schoolUserId, 'role' => 'school_student']);
    //         } else {
    //             $roleSlug = Role::where('role_slug', 'b2c_student')->first();
    //             if ($request->you_are_here_for == 'for_academic_content') {
    //                 StudentDetails::create(['user_id' => $data->id, 'class' => $request->class_id]);
    //             } else if ($request->you_are_here_for == 'both') {
    //                 StudentDetails::create(['user_id' => $data->id, 'class' => $request->class_id]);
    //             } else {
    //                 StudentDetails::create(['user_id' => $data->id]);
    //             }
    //             UserAdditionalDetail::create(['user_id' => $data->id, 'role' => 'b2c_student']);
    //         }
    //         $userRole            = new UserRole();
    //         $userRole->user_id   = $data->id;
    //         $userRole->role_slug = $roleSlug->role_slug ?? 'b2c_student';
    //         $userRole->save();

    //         $landingUi = getUserClassLandingUi() ?? null;
    //         $roleSlugFinal  = $data->userRole->role_slug ?? null;


    //         if ($data->save()) {
    //             $this->sendOtpMobile($data->mobile_no);
    //             return ApiResponseService::success(config('constants.API_MSG.MOBILE_NO_VERIFY'), [
    //                 'user'        => $data,
    //                 'role'        => $roleSlugFinal,
    //                 'landing_ui'  => $landingUi,
    //                 'otp_message' => 'OTP sent successfully on your ' . $data->mobile_no,
    //             ]);
    //         }

    //         return ApiResponseService::error(config('constants.API_STATUS_CODE.ERROR'), config('constants.API_MSG.REGISTRAION_ERROR'));
    //     } catch (ValidationException $e) {
    //         return $this->sendError(config('constants.API_MSG.VALIDATION_ERROR'), $e->errors(), 422);
    //     } catch (\Exception $e) {
    //         return ApiResponseService::error(500, config('constants.API_MSG.REGISTRAION_ERROR') . $e->getMessage());
    //     }
    // }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $this->formatCredentials(
                $request->only('username', 'password')
            );

            if (!Auth::attempt($credentials)) {
                return ApiResponseService::error(
                    config('constants.API_STATUS_CODE.ERROR'),
                    config('constants.API_MSG.INVALID_CREDENTIAL')
                );
            }

            $user = Auth::user();

            // Check inactive user
            if ($user->status == 0) {
                return ApiResponseService::error(
                    config('constants.API_STATUS_CODE.ERROR'),
                    '❌ Your account has been deactivated. Please contact support.'
                );
            }

            // Generate Sanctum token
            $token = $user->createToken($user->name . '_token')->plainTextToken;

            // Update user record
            $user->api_token = $token;
            $user->save();


            $userArray = $user->toArray();
            $userArray['image'] = !empty($user->image)
                ? 'https://mittlearn.com/storage/uploads/user/profile_image/' . $user->image
                : null;

            return ApiResponseService::success(
                "Login successful",
                [
                    "auth_token" => $token,
                    "role"       => $user->userRole->role_slug ?? null,
                    "user"       => $userArray
                ]
            );
        } catch (\Exception $e) {
            return ApiResponseService::error(
                500,
                "Login failed. " . $e->getMessage()
            );
        }
    }

    private function formatCredentials($credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return ['email' => $username, 'password' => $password];
        } elseif (preg_match('/^\d{10,15}$/', $username)) {
            return ['mobile_no' => $username, 'password' => $password];
        } else {
            return ['username' => $username, 'password' => $password];
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }





    // public function forgotPassword(Request $request)
    // {
    //     try {
    //         $request->validate(['username' => 'required|string']);

    //         $username = $request->username;
    //         $user     = $this->findUser($username);
    //         if (! $user) {
    //             return ApiResponseService::error(config('constants.API_STATUS_CODE.ERROR'), config('constants.API_MSG.NOT_FOUND_USER'));
    //         }

    //         if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
    //             $otp = $this->sendOtpEmail($username);
    //         } else {
    //             $otp = $this->sendOtpMobile($username);
    //         }

    //         return ApiResponseService::success('OTP sent successfully on your ' . $username, [
    //             'otp' => $otp,
    //         ]);
    //     } catch (ValidationException $e) {
    //         return $this->sendError(config('constants.API_MSG.VALIDATION_ERROR'), $e->errors(), 422);
    //     } catch (\Exception $e) {
    //         return ApiResponseService::error(500, config('constants.API_MSG.CALL_FAILED') . $e->getMessage());
    //     }
    // }

    // public function resetPassword(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'username'     => 'required|string',
    //             'otp'          => 'required|digits:6',
    //             'new_password' => 'nullable|string|min:8|confirmed',
    //         ]);

    //         $username = $request->username;
    //         $user     = $this->findUser($username);
    //         if (! $user) {
    //             return ApiResponseService::error(config('constants.API_STATUS_CODE.ERROR'), config('constants.API_MSG.NOT_FOUND_USER'));
    //         }

    //         $otpSession = OtpSession::where('mobile_email', $username)
    //             ->where('otp_verified', 0)
    //             ->where('expire_at', '>', now())
    //             ->orderBy('created_at', 'desc')
    //             ->first();

    //         if ($otpSession && $otpSession->otp === $request->otp) {
    //             $otpSession->otp_verified = 1;
    //             $otpSession->save();

    //             if ($request->filled('new_password')) {
    //                 $user->password        = Hash::make($request->new_password);
    //                 $user->validate_string = $request->new_password;
    //                 $user->save();
    //                 return ApiResponseService::success(config('constants.API_MSG.PASSWORD_RESET_SUCCESS'));
    //             }

    //             return ApiResponseService::success(config('constants.API_MSG.OTP_VERIFIED_SUCCESS'), [
    //                 'username' => $username,
    //             ]);
    //         }

    //         return ApiResponseService::error(config('constants.API_STATUS_CODE.ERROR'), config('constants.API_MSG.OTP_INVALID_EXPIRED'));
    //     } catch (ValidationException $e) {
    //         return $this->sendError(config('constants.API_MSG.VALIDATION_ERROR'), $e->errors(), 422);
    //     } catch (\Exception $e) {
    //         return ApiResponseService::error(500, config('constants.API_MSG.CALL_FAILED') . $e->getMessage());
    //     }
    // }

    // public function logout(Request $request)
    // {
    //     try {
    //         $user = $request->user(); // Authenticated user

    //         // Log logout time if login log ID is stored
    //         // if ($request->hasHeader('login-log-id')) {
    //         //     $logId = $request->header('login-log-id');
    //         //     UserLoginLog::where('id', $logId)->update(['logout_at' => now()]);
    //         // }

    //         // If you are using single-login logic
    //         $multipleLoginEnabled = Setting::where('field_name', 'multiple_login_enabled')->value('field_value');
    //         $allowedSingleLoginRoles = ['d2c_user', 'b2c_student', 'school_student'];
    //         $userRole = getUserRoles($user); // Adjust this if your helper accepts user

    //         if ($multipleLoginEnabled == 0 && in_array($userRole, $allowedSingleLoginRoles)) {
    //             $user->session_id = null;
    //             $user->save();
    //         }

    //         // Delete current access token
    //         $user->currentAccessToken()->delete();

    //         return ApiResponseService::success(config('constants.API_MSG.SUCCESS_LOGOUT'));
    //     } catch (\Exception $e) {
    //         return ApiResponseService::error(500, config('constants.API_MSG.FAILED_LOGOUT') . ' ' . $e->getMessage());
    //     }
    // }
}
