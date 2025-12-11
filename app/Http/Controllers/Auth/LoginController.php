<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CoreController;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    // use AuthenticatesUsers;
    public $data     = [];
    public $coreCtrl = '';
    public function __construct()
    {
        // $this->coreCtrl = CoreController::class;
    }
    public function index()
    {
        return view('auth.login-new');
    }

    public function loginSubmit(LoginRequest $request)
    {
        try {

            $username = $request->username;

            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $loginField = 'email';
            } elseif (is_numeric($username)) {
                $loginField = 'mobile_no';
            } else {
                return back()->with('error', 'Invalid username format.');
            }

            $credentials = [
                $loginField => $username,
                'password' => $request->password,
            ];
            if (!Auth::validate($credentials)) {
                return back()->with('error', config('constants.FLASH_INVALID_CREDENTIAL'));
            }
            $user = User::where($loginField, $username)->first();

            if (!$user) {
                return back()->with('error', 'User not found.');
            }
            if ($user->status == 0) {
                return back()->with('error', '❌ Your account is deactivated. Please contact the admin.');
            }
            Auth::login($user);

            if ($request->remember) {
                Cookie::queue('remember_username', $request->username, 43200); // 30 days
                Cookie::queue('remember_password', encrypt($request->password), 43200);
            } else {
                Cookie::queue(Cookie::forget('remember_username'));
                Cookie::queue(Cookie::forget('remember_password'));
            }

            return redirect()->route('dashboard')->with('success', 'Login Successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Login Failed: ' . $e->getMessage());
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

    public function logout()
    {
        Auth::logout();
        Session::invalidate();       // Clears session data
        Session::regenerateToken();  // Prevent CSRF reuse
        return redirect()->route('/')->with('success', 'You have successfully logged out.');
    }


    // public function loginEmailverifyCheck(Request $request)
    // {
    //     $data = $request->id;
    //     $otp  = implode('', $request->otp); // Concatenate the OTP array into a single string
    //     // dd();
    //     $attempt = OtpSession::where('mobile_email', $data)
    //         ->where('otp_verified', 0)
    //         ->where('otp', $otp)
    //         ->where('expire_at', '>', now())
    //         ->orderBy('created_at', 'desc')
    //         ->first();
    //     // dd($otp,$attempt);

    //     if ($attempt == null) {
    //         $error = 'Please enter a valid OTP or your OTP has expired.';
    //         return view("auth.email-verify", ['data' => $data, 'error' => $error]);
    //     }
    //     // Mark email as verified
    //     User::where('email', $data)
    //         ->update(['is_email_verified' => 1]);

    //     OtpSession::where('mobile_email', $data)->where('otp_verified', 0)
    //         ->update(['otp_verified' => 1]);
    //     // Log the user in
    //     $user = User::where('email', $data)->first();
    //     Auth::login($user);
    //     $userRole = getUserRoles(Auth::id());
    //     logUserLogin($user, $userRole, $request, 'web');

    //     if ($userRole == 'super_admin') {
    //         // Run the auto-logout logic after admin logs in
    //         Artisan::call('sessions:autologout');
    //     }
    //     if ($userRole == 'school_admin' || $userRole == 'school_teacher') {
    //         return redirect()->route('sp.dashboard')->with(['success' => 'Login Successfully']);
    //     }
    //     if ($userRole == 'super_admin') {
    //         return redirect()->route('dashboard')->with(['success' => 'Login Successfully']);
    //     }
    //     $landingUi = getUserClassLandingUi();
    //     if ($landingUi == 'mittbunny') {
    //         $this->storeStudentClass();
    //         return redirect()->route('mittbunny.dashboard')->with('success', 'Login Successfully');
    //     } else {
    //         $this->storeStudentOverview($request);
    //         return redirect()->route('up.dashboard')->with('success', 'Login Successfully');
    //     }
    //     // return redirect('/home')->with(['success' => 'Login Successfully']);
    // }

    // public function loginOtp()
    // {
    //     return view('auth.login_otp');
    // }

    // public function loginOtpFill(Request $request)
    // {
    //     $userId = $request->id ?? $request->userId;
    //     $otp    = OtpSession::where('session_id', $userId)->where('otp_verified', 0)->where('expire_at', '>', now())->orderBy('created_at', 'desc')->first();

    //     if ($otp) {
    //         return view('auth.login_otp_fill', ['userId' => $userId, 'activeTab' => 'Otp', 'otp' => $otp->otp]);
    //     }

    //     $isEmail = preg_match('/^[^@]+@[\w.-]+\.[a-zA-Z]{2,}$/', $userId);
    //     $user    = $isEmail ? User::where('email', $userId)->first() : User::where('mobile_no', $userId)->first();

    //     if (! $user) {
    //         return back()->with('error', 'Email/Mobile Number does not registered.');
    //     }

    //     if ($user->status == 0) {
    //         return back()->with('error', '❌ Your account is deactivated. Please contact the admin.');
    //     }
    //     if ($user->can_login == 0) {
    //         return back()->with('error', '❌ Your account is restricted from logging in because you do not have access to the digital content.');
    //     }

    //     if (! $otp) {
    //         $otpSession = OtpSession::where('session_id', $user->id)->where('otp_verified', 0)->where('expire_at', '>', now())->orderBy('created_at', 'desc')->first();

    //         if (! $otpSession) {
    //             $otpSession             = new OtpSession;
    //             $otpSession->session_id = $user->id;
    //         }
    //         $otp = random_int(100000, 999999);
    //         $otpSession->otp          = $otp;
    //         $otpSession->mobile_email = $isEmail ? $user->email : $user->mobile_no;
    //         $otpSession->expire_at    = now()->addMinutes(10);
    //         $otpSession->save();
    //         if ($otpSession) {
    //             $msg = 'Please wait, the OTP has been sent to your mobile number.';
    //             // Send OTP to user mobile
    //             $sent = sendSms($user->mobile_no, $otp, 'User');

    //             if (!$sent) {
    //                 return redirect()->back()->with('error', 'Failed to send OTP. Please try again.');
    //             }
    //             return view('auth.login_otp_fill', ['userId' => $userId, 'activeTab' => 'Otp', 'msg' => $msg]);
    //         }
    //     } else {
    //         return back()->with('error', 'Failed to generate OTP. Please try again.');
    //     }
    // }

    // public function resendOtp(Request $request)
    // {
    //     $mobileEmail = $request->input('mobile_email');
    //     $attempt     = OtpSession::where('mobile_email', $mobileEmail)->first();

    //     if (now()->isAfter($attempt->expire_at)) {
    //         $newOtp = rand(100000, 999999);
    //         session(['otp_value' => $newOtp]);
    //         DB::table('otp_sessions')
    //             ->where('mobile_email', $mobileEmail)
    //             ->update(['otp' => $newOtp, 'updated_at' => now()]);
    //         $sent = sendSms($mobileEmail, $newOtp, 'User');

    //         if (!$sent) {
    //             return redirect()->back()->with('error', 'Failed to send OTP. Please try again.');
    //         }
    //         return back()->with('success', 'OTP resent successfully.');
    //     } else {
    //         return back()->with('info', 'OTP is still valid. Please check your messages.');
    //     }
    // }

    // public function loginOtpCheck(Request $request)
    // {
    //     $userId   = $request->id;
    //     $otpArray = $request->input('otp');

    //     $otp     = implode('', $otpArray);
    //     $attempt = OtpSession::where('mobile_email', $userId)->where('otp', $otp)->first();

    //     if ($attempt && now()->isBefore($attempt->expire_at)) {
    //         $attempt->otp_verified = 1;
    //         $attempt->update();
    //         $isEmail = preg_match('/^[^@]+@[\w.-]+\.[a-zA-Z]{2,}$/', $userId);
    //         $user    = $isEmail ? User::where('email', $userId)->first() : User::where('mobile_no', $userId)->first();
    //         if ($user->status == 0) {
    //             return back()->with('error', '❌ Your account is deactivated. Please contact the admin.');
    //         }
    //         if ($user->can_login == 0) {
    //             return back()->with('error', '❌ Your account is restricted from logging in because you do not have access to the digital content.');
    //         }
    //         Auth::login($user);
    //         $userRole = getUserRoles();
    //         logUserLogin($user, $userRole, $request, 'web');
    //         if ($userRole == 'super_admin') {
    //             // Run the auto-logout logic after admin logs in
    //             Artisan::call('sessions:autologout');
    //         }
    //         if ($userRole == 'school_admin' || $userRole == 'school_teacher') {
    //             return redirect()->route('sp.dashboard')->with(['success' => 'Login Successfully']);
    //         }
    //         if ($userRole == 'super_admin') {
    //             return redirect()->route('dashboard')->with(['success' => 'Login Successfully']);
    //         }
    //         $allowedSingleLoginRoles = ['d2c_user', 'b2c_student', 'school_student'];
    //         $multipleLoginEnabled = Setting::where('field_name', 'multiple_login_enabled')->value('field_value');

    //         if ($multipleLoginEnabled == 0 && in_array($userRole, $allowedSingleLoginRoles)) {
    //             // Logout previous session if different
    //             if ($user->session_id && $user->session_id !== Session::getId()) {
    //                 DB::table('sessions')->where('id', $user->session_id)->delete();
    //             }
    //             if ($user->platform === 'app' && $user->api_token) {
    //                 $token = $user->tokens()->where('id', $user->api_token)->first();
    //                 if ($token) {
    //                     $token->delete();
    //                 }
    //                 $user->api_token = null;
    //             }
    //         }

    //         // Save current session ID to enforce single login
    //         $user->platform = 'web';
    //         $user->session_id = Session::getId();
    //         $user->is_mobile_verified = 1; // Mark mobile as verified
    //         $user->save();

    //         // $sent = sendSms($user->mobile_no, '', $user);

    //         $landingUi = getUserClassLandingUi();
    //         if ($landingUi == 'mittbunny') {
    //             $this->storeStudentClass();
    //             return redirect()->route('mittbunny.dashboard')->with('success', 'Login Successfully');
    //         } else {
    //             $this->storeStudentOverview($request);
    //             return redirect()->route('up.dashboard')->with('success', 'Login Successfully');
    //         }
    //     }
    //     return redirect()->route('login.otp.fill', ['userId' => $userId])
    //         ->with('error', 'Please enter a valid OTP or your OTP has expired.');
    // }



    // public function mergeCartWithLoggedInUser($guestUserId)
    // {
    //     // Check if the user is logged in and if there are guest cart items
    //     if (auth()->check() && $guestUserId) {
    //         $userId         = auth()->id();
    //         $guestCartItems = Cart::where('session_id', $guestUserId)->get();
    //         foreach ($guestCartItems as $item) {
    //             $item->update(['user_id' => $userId]);
    //         }
    //     }
    // }
    // public function storeStudentOverview(Request $request)
    // {
    //     $user = auth()->user();

    //     if (! $user) {
    //         return;
    //     }
    //     $createdAt = $user->created_at;
    //     $now = now();
    //     $freeAccessDaysLeft = null;

    //     if ($now->diffInDays($createdAt) < 15) {
    //         $freeAccessDaysLeft = 15 - $now->diffInDays($createdAt);
    //     }

    //     $request->merge(['from' => 'web']);
    //     $courses = $this->coreCtrl::getUserMyCourses($request);
    //     if (! empty($courses)) {
    //         $totalAcadCourses    = $courses['academic_courses']->count();
    //         $totalNonAcadCourses = $courses['nonacademic_courses']->count();
    //     } else {
    //         $totalAcadCourses    = 0;
    //         $totalNonAcadCourses = 0;
    //     }

    //     $completedAcadCourses    = 0;
    //     $completedNonAcadCourses = 0;

    //     if (! empty($courses)) {
    //         foreach ($courses['academic_courses'] as $course) {
    //             $userProgress = TrackUserVideoProgress::where('user_id', Auth::id())
    //                 ->where('course_id', $course->id);
    //             $totalVideoDuration   = $userProgress->sum('video_duration');
    //             $totalWatchedDuration = $userProgress->sum('watched_duration');
    //             if ($totalVideoDuration > 0 && $totalVideoDuration == $totalWatchedDuration) {
    //                 $completedAcadCourses++;
    //             }
    //         }
    //         foreach ($courses['nonacademic_courses'] as $course) {
    //             $userProgress = TrackUserVideoProgress::where('user_id', Auth::id())
    //                 ->where('course_id', $course->id);
    //             $totalVideoDuration   = $userProgress->sum('video_duration');
    //             $totalWatchedDuration = $userProgress->sum('watched_duration');
    //             if ($totalVideoDuration > 0 && $totalVideoDuration == $totalWatchedDuration) {
    //                 $completedNonAcadCourses++;
    //             }
    //         }
    //     }
    //     $acadCompletionPercentage = ($totalAcadCourses > 0)
    //         ? ($completedAcadCourses / $totalAcadCourses) * 100
    //         : 0;

    //     $nonAcadCompletionPercentage = ($totalNonAcadCourses > 0)
    //         ? ($completedNonAcadCourses / $totalNonAcadCourses) * 100
    //         : 0;

    //     $subscribedCourses = SubscriptionPurchase::where('user_id', Auth::id())->where('status', 'active')->first();
    //     if ($subscribedCourses) {
    //         $courses                = json_decode($subscribedCourses->courses_json, true);
    //         $totalSubscribedCourses = count($courses['academic_courses']) + count($courses['non_academic_courses']);
    //     } else {
    //         $totalSubscribedCourses = 0;
    //     }

    //     $studentDetails = [
    //         'name'                        => ucwords($user->name),
    //         'image'                       => $user->image ? Storage::url('uploads/user/profile_image/' . $user->image) : asset('frontend/images/default-image.jpg'),
    //         'class'                       => Auth::user()?->studentDetails?->className?->name ?? null,
    //         'plan_start'                  => '12/02/2023',
    //         'plan_expiry'                 => '12/02/2023',
    //         'parent_name'                 => optional($user->studentDetails)->parent_name ? ucwords($user->studentDetails->parent_name) : 'N/A',
    //         'subscribed_courses'          => 4,
    //         'completed_tasks'             => 12,
    //         'totalSubscribedCourses'      => $totalSubscribedCourses ?? '0',
    //         'subscribedCourses'           => $subscribedCourses,
    //         'totalAcadCourses'            => $totalAcadCourses,
    //         'totalNonAcadCourses'         => $totalNonAcadCourses,
    //         'completedAcadCourses'        => $completedAcadCourses,
    //         'completedNonAcadCourses'     => $completedNonAcadCourses,
    //         'acadCompletionPercentage'    => round($acadCompletionPercentage, 2),
    //         'nonAcadCompletionPercentage' => round($nonAcadCompletionPercentage, 2),
    //         'free_access_days_left '      => $freeAccessDaysLeft,
    //     ];
    //     Session::put('student_overview', $studentDetails);
    // }
    // public function storeStudentClass()
    // {
    //     $user = auth()->user();

    //     if (! $user) {
    //         return;
    //     }
    //     $studentDetails = [
    //         'class' => Auth::user()?->studentDetails?->className?->name ?? null,
    //     ];

    //     Session::put('student_class', $studentDetails);
    // }
}
