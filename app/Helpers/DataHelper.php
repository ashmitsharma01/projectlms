<?php

use App\Models\AlertTemplate;
use App\Models\BookSeries;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Classes;
use App\Models\FrontendCoursesView;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\SchoolAssignedClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolClass;
use App\Models\Schools;
use App\Models\Setting;
use App\Models\StudentDetails;
use App\Models\Subject;
use App\Models\SubscriptionPlanPack;
use App\Models\User;
use App\Models\UserAdditionalDetail;
use App\Models\UserPermission;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\UserLoginLog;
use Illuminate\Support\Facades\Session;

function getPaginationNum($num = 10)
{
    return $num;
}

function getCacheKey($key)
{
    $num = Auth::user() ? Auth::user()->id : date('ymd');
    return $key . '_' . $num;
}

function removeCacheKeys($key = null)
{
    if ($key) {
        Cache::forget($key);
    } else {
        Cache::flush();
    }
    return true;
}

function getAuthUserInfo($info = 'all')
{
    $user = Auth::user() ? Auth::user() : null;

    if ($info == 'id') {
        return $user->id;
    }

    $user->id      = $user->id ?? '';
    $user->role    = $user->userRole->role_slug ?? '';
    $user->role_id = $user->userRole->role->id ?? '';
    return $user;
}
function getUserRoles($userId = '')
{
    $userId   = $userId ? $userId : Auth::id();
    $userRole = UserRole::where('user_id', $userId)->value('role_slug');
    return $userRole;
}
function getUserBoard($userId = '')
{
    $role = getUserRoles();
    if ($role == 'school_admin') {
        $board = UserAdditionalDetail::where('user_id', Auth::id())->value('school_board');
    } else if ($role == 'school_teacher') {
        $schoolId     = UserAdditionalDetail::where('user_id', Auth::id())->value('school_id');
        $board        = UserAdditionalDetail::where('user_id', $schoolId)->value('school_board');
    }
    return $board;
}
function getUserMedium($userId = '')
{
    $role = getUserRoles();
    if ($role == 'school_admin') {
        $medium = UserAdditionalDetail::where('user_id', Auth::id())->value('school_medium');
    } else if ($role == 'school_teacher') {
        $schoolId     = UserAdditionalDetail::where('user_id', Auth::id())->value('school_id');
        // $schoolUserId = Schools::where('id', $schoolId)->value('user_id');
        $medium       = UserAdditionalDetail::where('user_id', $schoolId)->value('school_medium');
    }
    return $medium;
}

function getUserClassLandingUi()
{
    $userClass = Auth::user()->studentDetails->class ?? null;
    $userUi    = Classes::where('id', $userClass)->value('landing_ui') ?? null;
    return $userUi;
}
function getTeacherAssignedClasses()
{
    $authId = Auth::id();

    $assignedClasses = UserAdditionalDetail::where('user_id', $authId)
        ->value('assigned_classes');
    return $assignedClasses ? explode(',', $assignedClasses) : [];
}
function getTeacherAssignedSubjects()
{
    $authId = Auth::id();

    $assignedSubjects = UserAdditionalDetail::where('user_id', $authId)
        ->value('assigned_subjects');

    return $assignedSubjects ? explode(',', $assignedSubjects) : [];
}

function getUserSchoolBoard()
{
    $authId = Auth::id();

    $schoolId = StudentDetails::where('user_id', $authId)
        ->value('school_id');
    $board = UserAdditionalDetail::where('user_id', $schoolId)->value('school_board');
    return $board;
    // return $board ? explode(',', $board) : [];
}
function getUserSchoolMedium()
{
    $authId = Auth::id();

    $schoolId = StudentDetails::where('user_id', $authId)
        ->value('school_id');
    $medium = UserAdditionalDetail::where('user_id', $schoolId)->value('school_medium');

    return $medium;
    // return $medium ? explode(',', $medium) : [];

}
function getUserSchoolClasses($schoolId)
{
    $schoolAssignedClasses = SchoolAssignedClass::with('class')->where('school_id', $schoolId)
        ->pluck('class_id')
        ->toArray();
    $schoolClasses = SchoolClass::whereIn('id', $schoolAssignedClasses)->pluck('name', 'id')->toArray();
    return $schoolClasses;
}
function getTeacherClasses($teacherId, $schoolId)
{
    $teacherAssignedClasses = UserAdditionalDetail::where('user_id', $teacherId)->where('school_id', $schoolId)
        ->first();
    return explode(',', $teacherAssignedClasses->assigned_classes);
}
function getTeacherSubject($teacherId, $schoolId)
{
    $teacherAssignedSubject = UserAdditionalDetail::where('user_id', $teacherId)->where('school_id', $schoolId)
        ->first();
    return explode(',', $teacherAssignedSubject->assigned_subjects);
}
function getSchoolAssignedSubjects($schoolId)
{
    $schoolSubjects = SchoolAssignedDigitalContent::where('school_id', $schoolId)
        ->pluck('subject_id')
        ->flatMap(fn($ids) => explode(',', $ids))
        ->filter()
        ->unique()
        ->values()
        ->toArray();
    return $schoolSubjects;
}
function getUserSchoolSeries()
{
    $schoolId    = Auth::user()->studentDetails->school_id ?? '';
    $userClass    = Auth::user()->studentDetails->class ?? '';

    $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $schoolId)->where('class_id', $userClass)
        ->value('series_id');
    return $schoolAssignedSeries;
}
function getUserSchoolSubjects()
{
    $schoolId    = Auth::user()->studentDetails->school_id ?? '';
    $userClass    = Auth::user()->studentDetails->class ?? '';

    $schoolAssignedSubjects = SchoolAssignedDigitalContent::where('school_id', $schoolId)->where('class_id', $userClass)
        ->value('subject_id');

    return explode(',', $schoolAssignedSubjects);
}

function getRolesFromCache()
{
    $roles    = [];
    $cacheKey = getCacheKey('rolesListCache');
    if (Cache::has($cacheKey)) {
        $roles = Cache::get($cacheKey);
    } else {
        $roles = Role::pluck('role_slug', 'role_slug')->toArray();
        Cache::put($cacheKey, $roles, config('constants.CACHING_TIME'));
    }
    return $roles;
}
function getRoles()
{
    $roles = Role::whereIsActive(1)->pluck('role_name', 'id');
    return $roles;
}
function getAssignedPermissionsByRoleUser($roleId, $userId = '')
{
    $rolePermissions = RolePermission::whereRoleId($roleId)->pluck('permission_id');
    $userPermissions = UserPermission::whereUserId($userId)->pluck('permission_id');

    $assignedPermissions = [...$rolePermissions, ...$userPermissions];
    return $assignedPermissions;
}
function getFormatedPermissionsList($permissions)
{
    $userInfo = getAuthUserInfo();
    //$roles = getRolesFromCache();
    $assignedPermissions = getAssignedPermissionsByRoleUser($userInfo->role_id, $userInfo->id);
    $permissionArr       = [];
    //dd($assignedPermissions);
    if ($permissions && $userInfo->role) {
        foreach ($permissions as $permission) {
            // Check if the permission ID exists in the assigned permissions
            if (in_array($permission->id, $assignedPermissions)) {
                $permissionArr[$permission->slug] = 1;
            }
        }
    }
    return $permissionArr;
}

function getPermissions($type)
{
    $permissionArr = [];
    if ($type == 'menu') {
        $cacheKey = getCacheKey('menuPermissionListCache');
        if (Cache::has($cacheKey)) {
            $permissionArr = Cache::get($cacheKey);
        } else {
            $permissions   = Permission::where('permission_type', 'menu')->get();
            $permissionArr = getFormatedPermissionsList($permissions);
            Cache::put($cacheKey, $permissionArr, config('constants.CACHING_TIME'));
        }
    } else if ($type == 'route') {
        $cacheKey = getCacheKey('routePermissionListCache');
        // if (Cache::has($cacheKey)) {
        //     $permissionArr = Cache::get($cacheKey);
        // } else {
        $permissions   = Permission::where('permission_type', 'route')->get();
        $permissionArr = getFormatedPermissionsList($permissions);
        Cache::put($cacheKey, $permissionArr, config('constants.CACHING_TIME'));
        //}
    } else if ($type == 'app_menu') {
        $cacheKey = getCacheKey('routePermissionListCache');
        // if (Cache::has($cacheKey)) {
        //     $permissionArr = Cache::get($cacheKey);
        // } else {
        $permissions   = Permission::where('permission_type', 'app_menu')->get();
        $permissionArr = getFormatedPermissionsList($permissions);
        Cache::put($cacheKey, $permissionArr, config('constants.CACHING_TIME'));
        //}
    } else if ($type == 'other') {
        $cacheKey = getCacheKey('otherPermissionListCache');
        if (Cache::has($cacheKey)) {
            $permissionArr = Cache::get($cacheKey);
        } else {
            $permissions   = Permission::whereNotIn('permission_type', ['route', 'menu'])->get();
            $permissionArr = getFormatedPermissionsList($permissions);
            Cache::put($cacheKey, $permissionArr, config('constants.CACHING_TIME'));
        }
    }
    return $permissionArr;
}
function getMenuPermission()
{
    return getPermissions('menu');
}
function getRoutePermission()
{
    return getPermissions('route');
}
function getOtherPermission()
{
    return getPermissions('other');
}
function isPermission($route)
{
    $pRoute        = getRoutePermission();
    $pOther        = getOtherPermission();
    $permissionArr = [...$pRoute, ...$pOther];
    // Check if the parameter is an array
    if (is_array($route)) {
        // Check if any one route passes
        foreach ($route as $r) {
            if (isset($permissionArr[$r]) && $permissionArr[$r] == 1) {
                return true; // Return true if any route passes
            }
        }
        return false; // If none of the routes pass, return false
    }

    // For string input, original logic
    if (is_string($route)) {
        if (isset($permissionArr[$route]) && $permissionArr[$route] == 1) {
            return true;
        }
    }
    return false;
}
//
function getFrontendWebsiteVisibleCourses()
{
    return FrontendCoursesView::get();
}
function getParentCategories()
{
    $data = Category::where('status', 1)->whereNull('parent_id')->pluck('name', 'id');
    return $data;
}
function getAcademicCategoriesWithChild()
{
    $data = Category::where('status', 1)->with('children')->whereNotNull('parent_id')->whereParentId(1)->get();
    return $data;
}
function getCategoriesWithChild()
{
    $data = Category::where('status', 1)->with('children')->whereNotNull('parent_id')->whereParentId(2)->get();
    return $data;
}
function getClasses()
{
    $data = Classes::whereIsActive(1)->pluck('name', 'id');
    return $data;
}
function getSubjects()
{
    $data = Subject::whereIsActive(1)->pluck('name', 'id');
    return $data;
}
function getBookSeries()
{
    $data = BookSeries::whereIsActive(1)->pluck('name', 'id');
    return $data;
}

//

function checkFile($filename, $path, $default = null)
{
    $src = $default ? asset('images/' . $default) : asset('images/mittlearn-favicon.png');

    $storagePath = $path . $filename;
    if ($filename != null && $filename != '' && $filename != '0') {
        if ($filename && Storage::exists($storagePath)) {
            $src = Storage::url($storagePath);
        }
    }
    return $src;
}
function unlinkImg($img, $path)
{
    if ($img != null || $img != '') {
        $path       = 'public/' . $path;
        $image_path = app()->basePath($path . $img);
        if (File::exists($image_path)) {
            unlink($image_path);
        }
    }
}

function getStatusBtn($status, $listType = 1, $optionalParams = [])
{
    $statusList = config('constants.STATUS_LIST');
    $btnClass   = ['text-danger', 'text-success', 'text-danger'];
    if ($listType == 2) {
        $statusList = config('constants.LIST2_STATUS');
    } else if ($listType == 3) {
        $btnClass   = ['text-default', 'text-primary', 'text-success'];
        $statusList = config('constants.LIST_');
    }

    $txt = '';
    if (isset($statusList[$status])) {
        $txt = $statusList[$status];
    }

    $badgeClass = (isset($optionalParams['badge_class'])) ? $optionalParams['badge_class'] : '';
    if ($badgeClass != '') {
        $btnClass[$status] = str_replace('text', 'bg', $btnClass[$status]) . ' ' . $badgeClass;
    }

    if ($status == 1) {
        return '<span class="badge ' . $btnClass[$status] . '">' . $txt . '</span>';
    }
    if ($status == 2) {
        return '<span class="badge ' . $btnClass[$status] . '">' . $txt . '</span>';
    }
    if ($status == 3) {
        return '<span class="badge ' . $btnClass[$status] . '">' . $txt . '</span>';
    } else {
        return '<span class="badge ' . $btnClass[$status] . '">' . $txt . '</span>';
    }
}

function getCurrencyList()
{
    $datalist     = [];
    $currencyList = config('constants.CURRENCY_LIST');
    foreach ($currencyList as $val) {
        if ($val['status'] == 1) {
            $datalist[$val['code']] = $val['code'];
        }
    }
    return $datalist;
}
function getConstants($list, $exclude = [])
{
    if ($list == 'ENABLE') {
        return config('constants.ENABLE');
    }
    return null;
}
function getDurationTypeList()
{
    $datalist     = [];
    $currencyList = config('constants.DURATION_TYPES');
    foreach ($currencyList as $val) {
        if ($val['status'] == 1) {
            $datalist[$val['value']] = $val['label'];
        }
    }
    return $datalist;
}

function calculatePlanFinalPrice($priceRowData)
{
    $discountValue = (float) $priceRowData['discount_value'];
    $price         = (float) $priceRowData['price'];

    if ($priceRowData['discount_type'] == 'percent') {
        $discountAmount = ($discountValue > 0) ? ($price * $discountValue) / 100 : 0;
        $finalAmount    = $price - $discountAmount;
    } else {
        $finalAmount = $price - $discountValue;
    }
    return numFormat($finalAmount);
}

function getCourseSubData($courseRow)
{
    $dataArr = [
        'board_info'       => null,
        'class_info'       => null,
        'subject_info'     => null,
        'book_series_info' => null,
    ];
    if ($courseRow->metadata) {
        foreach ($courseRow->metadata as $row) {
            if ($row->field_name == 'board') {
                $dataArr['board_info'] = $row->boardInfo;
            } else if ($row->field_name == 'class') {
                $dataArr['class_info'] = $row->classInfo;
            } else if ($row->field_name == 'subject') {
                $dataArr['subject_info'] = $row->subjectInfo;
            } else if ($row->field_name == 'series') {
                $dataArr['book_series_info'] = $row->bookseriesInfo;
            }
        }
    }
    return $dataArr;
}

function categoriesToArray($categories)
{
    return $categories->map(function ($category) {
        $dataArr = [
            'id'    => $category->id,
            //'parent_id' => $category->parent_id,
            //'slug' => $category->slug,
            //'name' => $category->name,
            'text'  => $category->name,
            'title' => $category->name,
            //'description' => $category->description,
            //'children' => categoriesToArray($category->children) // Recursively get children
        ];

        $childArr = categoriesToArray($category->children);
        if (count($childArr)) {
            $dataArr['subs'] = $childArr;
        }
        return $dataArr;
    });
}

function getCourseSubInfoForDisplay($subData)
{
    return 'Board: <span>' . ($subData['board_info']->name ?? 'NA') . '</span> | ' .
        'Class: ' . ($subData['class_info']->name ?? 'NA') . ' | ' .
        'Series: ' . ($subData['book_series_info']->name ?? 'NA') . ' | ' .
        'Subject: ' . ($subData['subject_info']->name ?? 'NA');
}

function compressImage($file, $destinationPath, $quality = 50)
{
    // dd($file)
    $originalImage = imagecreatefromstring(file_get_contents($file->getRealPath()));
    imagejpeg($originalImage, $destinationPath, $quality);
    imagedestroy($originalImage);

    return $destinationPath;
}
function compressAndResizeImage($file, $destinationPath, $width = 400, $height = 400, $quality = 50)
{
    $originalImage   = imagecreatefromstring(file_get_contents($file->getRealPath()));
    $compressedImage = imagescale($originalImage, $width, $height);
    imagejpeg($compressedImage, $destinationPath, $quality);
    imagedestroy($originalImage);
    imagedestroy($compressedImage);

    return $destinationPath;
}
function generateUniqueSlug(string $title, string $modelClass, string $slugField = 'slug', ?int $ignoreId = null): string
{
    // Generate the initial slug
    $slug         = Str::slug($title);
    $originalSlug = $slug;
    $counter      = 1;

    // Create a new model instance to get its primary key name
    $modelInstance = new $modelClass;

    // Check for uniqueness
    while ($modelClass::where($slugField, $slug)
        ->when($ignoreId, function ($query) use ($ignoreId, $modelInstance) {
            $query->where($modelInstance->getKeyName(), '!=', $ignoreId);
        })
        ->exists()
    ) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}
function checkDiscount($planId, $session_id)
{
    $cartItemCount = Cart::where(function ($query) use ($session_id) {
        $query->where('session_id', $session_id)->where('status', 'active');
    })
        ->orWhere(function ($query) {
            $query->where('user_id', Auth::id())->where('status', 'active');
        })
        ->count();

    if (! $cartItemCount) {
        return [
            'status'  => 'error',
            'message' => 'Cart not found for this user',
        ];
    }

    $planPacks = SubscriptionPlanPack::where('plan_id', $planId)->orderBy('set_of_courses')->get();

    if ($planPacks->isEmpty()) {
        return [
            'status'  => 'info',
            'message' => 'No discounts available for this plan',
        ];
    }

    $nearestPlanPack        = null;
    $currentPack            = null;
    $remainingCourses       = 0;
    $discount               = 0;
    $discountType           = '';
    $currentDiscount        = 0; // Add current discount
    $currentDiscountType    = '';
    $freeAcademicCourses    = '';
    $freeNonAcademicCourses = '';
    foreach ($planPacks as $planPack) {
        if (! $nearestPlanPack && $planPack->set_of_courses >= $cartItemCount) {
            $nearestPlanPack  = $planPack;
            $remainingCourses = $planPack->set_of_courses - $cartItemCount;
            $discount         = (int) $planPack->discount_value;
            $discountType     = $planPack->discount_type;
        }

        // Get currentPack (plan with set_of_courses <= cartItemCount)
        if ($planPack->set_of_courses <= $cartItemCount) {
            $currentPack            = $planPack;
            $currentDiscount        = (int) $planPack->discount_value;
            $currentDiscountType    = $planPack->discount_type;
            $freeAcademicCourses    = $planPack->free_course_academic ?? '';
            $freeNonAcademicCourses = $planPack->free_course_non_academic ?? '';
        }
    }

    if (! $nearestPlanPack) {
        return [
            'status'  => 'info',
            'message' => 'No eligible discounts found',
        ];
    }

    return [
        'status'                   => 'success',
        'discount'                 => [
            'value' => $discount,
            'type'  => $discountType,
        ],
        'remaining_courses'        => $remainingCourses,
        'current_discount'         => $currentDiscount,
        'current_discount_type'    => $currentDiscountType,
        'free_academic_courses'    => $freeAcademicCourses,
        'free_nonacademic_courses' => $freeNonAcademicCourses,
    ];
}

function calculatePlannerCompletionDate($startDate, $allottedDays, $skipSundays = true, $skipHolidays = false)
{
    $startDateCarbon = Carbon::parse($startDate);

    // Load holidays from the holidays tbl (this can be replaced with actual data fetching)
    $holidays  = [];
    $addedDays = 1;

    while ($addedDays < $allottedDays) {

        $startDateCarbon->addDay();
        if ($skipSundays && $startDateCarbon->isSunday()) {
            continue;
        }
        if ($skipHolidays && in_array($startDateCarbon->toDateString(), $holidays)) {
            continue;
        }
        $addedDays++;
    }
    return $startDateCarbon->format('Y-m-d');
}
function getSettings()
{
    $settings = Setting::all()->pluck('field_value', 'field_name')->toArray();
    return $settings;
}
function sendEmail($templateId, $email, $data = null, $bccMode = false)
{
    $settings = getSettings();

    if (
        ! isset($settings['mail_mailer']) ||
        ! isset($settings['mail_host']) ||
        ! isset($settings['mail_port']) ||
        ! isset($settings['mail_user_name']) ||
        ! isset($settings['mail_password']) ||
        ! isset($settings['mail_encryption']) ||
        ! isset($settings['from_mail_address'])
    ) {
        return false;
    }

    // Ensure proper from name
    $fromName = ! empty($settings['from_mail_name'])
        ? $settings['from_mail_name']
        : 'Mittlearn | Mittsure Technologies';

    // Update mail configuration dynamically
    config([
        'mail.mailers.smtp.transport'  => 'smtp',
        'mail.mailers.smtp.host'       => $settings['mail_host'],
        'mail.mailers.smtp.port'       => $settings['mail_port'],
        'mail.mailers.smtp.encryption' => $settings['mail_encryption'],
        'mail.mailers.smtp.username'   => $settings['mail_user_name'],
        'mail.mailers.smtp.password'   => $settings['mail_password'],
        'mail.from.address'            => $settings['from_mail_address'],
        'mail.from.name'               => 'Mittlearn | Mittsure Technologies',
    ]);

    $template = AlertTemplate::find($templateId);
    if (! $template) {
        return false;
    }

    // Replace placeholders in the email template
    $placeholders = array_map(function ($key) {
        return '{' . strtoupper($key) . '}';
    }, array_keys($data));

    $replacements = array_values($data);
    $subject      = str_replace($placeholders, $replacements, $template->subject);
    $body         = str_replace($placeholders, $replacements, $template->body);

    try {
        Mail::send('emails.template', ['messageBody' => $body, 'subject' => $subject], function ($message) use ($email, $subject, $fromName, $settings, $bccMode) {
            if ($bccMode) {
                // If called in BCC mode (e.g., from ticket system)
                $message->to($settings['from_mail_address'])
                    ->bcc($email)
                    ->subject($subject)
                    ->from($settings['from_mail_address'], $fromName);
            } else {
                // Normal behavior (for all other places)
                $message->to($email)
                    ->subject($subject)
                    ->from($settings['from_mail_address'], $fromName);
            }
        });

        // Mail::send('emails.template', ['messageBody' => $body, 'subject' => $subject], function ($message) use ($email, $subject, $fromName, $settings) {
        //     $message->to($email)
        //         ->subject($subject)
        //         ->from($settings['from_mail_address'], $fromName);
        // });
        return true;
    } catch (\Exception $e) {
        \Log::error('Mail sending failed: ' . $e->getMessage());
        return false;
    }
}

function convertTo12HourFormat($time)
{
    $dateTime = \DateTime::createFromFormat('H:i', $time);
    return $dateTime->format('g:i A');
}

function sendSmsWithTemplate($templateId, $mobile, $data = null)
{
    $settings = getSettings();
    if (! isset($settings['sms_sender_id']) || ! isset($settings['sms_api_key']) || ! isset($settings['sms_api_url']) || ! isset($settings['sms_api_username'])) {
        return false;
    }
    $senderId    = @$settings['sms_sender_id'];
    $apiKey      = @$settings['sms_api_key'];
    $apiUrl      = @$settings['sms_api_url'];
    $apiUsername = @$settings['sms_api_username'];

    $templateData = AlertTemplate::whereId($templateId)->first();
    if (!$templateData) {
        return false;
    }

    $name = $data['name'] ?? '';
    $otp  = $data['otp']  ?? '';

    $smsTxt = str_replace(
        ['##NAME##', '##OTP##'],
        [$name, $otp],
        $templateData->template
    );

    $message = urlencode($smsTxt);

    $send_url = $apiUrl . "?username=" . $apiUsername . "&message=" . $message . "&sendername=" . $senderId . "&smstype=TRANS&numbers=" . $mobile . "&apikey=" . $apiKey;
    $send_url = trim($send_url);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => $send_url,
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    return true;
}
function sendSms($mobile, $otp, $name)
{
    $settings = getSettings();
    if (
        !isset($settings['sms_sender_id']) ||
        !isset($settings['sms_api_key']) ||
        !isset($settings['sms_api_url'])
    ) {
        return false;
    }

    $senderId   = $settings['sms_sender_id'];
    $apiKey     = $settings['sms_api_key'];
    $apiUrl     = $settings['sms_api_url'];

    // Variables for message and template ID
    $smsTxt = '';
    $templateId = '';

    if (!$otp && $name !== null) {
        $smsTxt = "Welcome to Mittlearn! Your access detail is: User ID : {$mobile} Password : {$name->validate_string} https://mittlearn.com/login Thanks, Mittsure";
        $templateId = '1707175342479039432'; // Approved Login Details Template ID
        $campaignName = 'Mittlearn Login OTP'; // Approved Login Details Template ID
    } else if ($otp) {
        // OTP template
        $smsTxt = "{$otp} is your OTP for Mittlearn account login/ reset. It is valid for one-time use only. Thanks, Mittsure.";
        $templateId = '1707175342420905177'; // Approved OTP Template ID
        $campaignName = 'Mittlearn Welcome/Login'; // Approved OTP Template ID
    } else {
        // Fallback template (must be DLT approved separately)
        $smsTxt = "Welcome to Mittlearn! Please contact support for your login details.";
        $templateId = ''; // <-- Register and replace with approved Template ID
    }

    // Prepare POST data
    $smsData = [
        'campaign_name' => $campaignName ?? 'Mittsure',
        'auth_key'      => $apiKey,
        'receivers'     => $mobile,
        'sender'        => $senderId,
        'route'         => 'TR',
        'message'       => [
            'msgdata'       => $smsTxt,
            'Template_ID'   => $templateId,
            'coding'        => '1',
            'scheduleTime'  => now()->toDateTimeString(),
        ],
    ];

    // Send POST request using Laravel Http Client
    $response = Http::post($apiUrl, $smsData);

    // Log the full raw response with timestamp
    \Log::info('SMS API Full Response', [
        'timestamp' => now()->toDateTimeString(),
        'mobile'    => $mobile,
        'request'   => $smsData,
        'response'  => $response->json() ?: $response->body()
    ]);



    if ($response->successful()) {
        return true;
    } else {
        \Log::error('SMS sending failed', [
            'response' => $response->body(),
            'mobile'   => $mobile,
            'smsTxt'   => $smsTxt,
            'templateId' => $templateId
        ]);
        return false;
    }
}

function logUserLogin($user, $role, $request, $platform)
{
    // Prevent super_admin login logging
    if ($role === 'super_admin') {
        return;
    }

    $existingLogin = UserLoginLog::where('user_id', $user->id)
        ->whereDate('login_at', now()->toDateString())
        ->whereNull('logout_at')
        ->first();

    if (! $existingLogin) {
        $loginLog = UserLoginLog::create([
            'user_id'    => $user->id,
            'role'       => $role,
            'login_at'   => now(),
            'ip_address' => $request->ip(),
            'platform'   => $platform,
        ]);

        Session::put('login_log_id', $loginLog->id);
    }
}
