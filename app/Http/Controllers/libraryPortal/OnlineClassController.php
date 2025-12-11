<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Models\MediaFiles;
use App\Models\OnlineClass;
use App\Models\SchoolAssignedClass;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OnlineClassController extends Controller
{
    public $data = [];
    public function show(Request $request)
    {
        $role     = getUserRoles();
        $parentId = Auth::id();

        // If the role is "school_teacher", adjust parentId and use instructor_id for fetching classes
        if ($role == "school_teacher") {
            $parentId = Auth::user()->userAdditionalDetail->school_id;

            // Fetch assigned subjects and classes for the teacher
            $teacherAssignedSubjects = getTeacherAssignedSubjects(); // Returns an array of subject IDs
            $teacherAssignedClasses  = getTeacherAssignedClasses();  // Returns an array of class IDs

            // Fetch online classes based on instructor_id
            $this->data['ongoingClasses'] = OnlineClass::where('instructor_id', Auth::id())
                ->where('status', 'ongoing')
                ->with(['instructor', 'class', 'subject'])
                ->get();

            $this->data['pastClasses'] = OnlineClass::where('instructor_id', Auth::id())
                ->where('status', 'past')
                ->with(['instructor', 'class', 'subject', 'joinLogs'])
                ->get();

            $this->data['upcomingClasses'] = OnlineClass::where('instructor_id', Auth::id())
                ->where('status', 'upcoming')
                ->with(['instructor', 'class', 'subject'])
                ->get();

            // Fetch classes and subjects assigned to the teacher
            $this->data['classes']  = SchoolClass::whereIn('id', $teacherAssignedClasses)->pluck('name', 'id')->toArray();
            $this->data['subjects'] = Subject::whereIn('id', $teacherAssignedSubjects)->pluck('name', 'id')->toArray();
        } else {
            $this->data['classes']        = getUserSchoolClasses($parentId);
            $subjects        = getSchoolAssignedSubjects($parentId);


            $this->data['subjects']  = Subject::whereIn('id', $subjects)
                ->pluck('name', 'id')
                ->toArray();

            // Fetch online classes based on parent_id for other roles
            $this->data['ongoingClasses'] = OnlineClass::where('parent_id', $parentId)
                ->where('status', 'ongoing')
                ->with(['instructor', 'class', 'subject'])
                ->get();

            $this->data['pastClasses'] = OnlineClass::where('parent_id', $parentId)
                ->where('status', 'past')
                ->with(['instructor', 'class', 'subject', 'joinLogs'])
                ->get();

            foreach ($this->data['pastClasses'] as $pastClass) {
                $pastClass->uniqueJoinLogs = $pastClass->joinLogs()->with('user')->get()->unique('user_id');
            }

            $this->data['upcomingClasses'] = OnlineClass::where('parent_id', $parentId)
                ->where('status', 'upcoming')
                ->with(['instructor', 'class', 'subject'])
                ->get();

            // Fetch all classes and subjects for other roles
            // $this->data['classes']  = SchoolClass::where('is_active', 1)->get();
            // $this->data['subjects'] = Subject::where('is_active', 1)->get();
        }

        // Fetch additional data (teachers)
        $this->data['teachers'] = User::with('userAdditionalDetail')
            ->whereHas('userAdditionalDetail', function ($query) use ($parentId) {
                $query->where('role', 'school_teacher')
                    ->where('school_id', $parentId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('schoolPortal.onlineClass.online_class', $this->data);
    }

    public function store(Request $request)
    {
        $role     = getUserRoles();
        $parentId = Auth::id();

        // If the role is "school_teacher", adjust parentId and use instructor_id for fetching classes
        if ($role == "school_teacher") {
            $parentId = Auth::user()->userAdditionalDetail->school_id;
        }
        // Validate the incoming request data
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'class_date'    => 'required|date',
            'class_id'      => 'required|integer',
            'subject_id'    => 'required|integer',
            'instructor_id' => $role === 'school_teacher' ? 'nullable' : 'required|integer', // Conditionally required
            'start_time'    => 'required',
            'end_time'      => 'required|after:start_time',
            'join_link'     => 'required|url',
            'agenda'        => 'required|string|max:200',
        ]);

        $validated['parent_id'] = $parentId;

        if ($role === 'school_teacher') {
            $validated['instructor_id'] = Auth::id();
        }
        $class = OnlineClass::create($validated);

        // Send notification if the class creation is successful
        if ($class) {
            $this->sendNotification($request);
        }
        return redirect()->back()->with('success', 'Online class created successfully!');
    }

    public function onlineClassDetails($id)
    {
        $this->data['data']          = OnlineClass::where('id', $id)->where('status', 'past')->with(['instructor', 'class', 'subject'])->get();
        $this->data['studyMaterial'] = MediaFiles::where('tbl_id', $id)->where('type', 'online_class_study_material')->get();
        // dd($this->data['studyMaterial']);
        return view('schoolPortal.onlineClass.online_class_details', $this->data);
    }

    public function storeFile(Request $request)
    {
        $maxFileSize = config('constants.MAX_FILE_SIZE');
        $request->validate([
            'file' => "required|file|mimes:jpg,jpeg,png,bmp,gif,svg,doc,docx,xls,xlsx,pdf,mp3,wav,mp4,mov,avi|max:$maxFileSize",
        ]);

        try {
            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            // Check if the file is an image before compressing
            $imageMimeTypes = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'];
            if (in_array($extension, $imageMimeTypes)) {
                // Compress the image
                $compressedImagePath = storage_path('app/public/uploads/media-files/' . time() . '.' . $extension);
                compressImage($file, $compressedImagePath);

                $fileName = basename($compressedImagePath); // Extract file name after compression
                $path     = 'uploads/media-files/' . $fileName;
            } else {
                // For non-image files, upload without compression
                $fileName = time() . '.' . $extension;
                $path     = Storage::disk('public')->put('uploads/media-files/' . $fileName, file_get_contents($file));
            }

            if ($path) {
                $mediaFile                  = new MediaFiles();
                $mediaFile->tbl_id          = $request->class_id;
                $mediaFile->type            = 'online_class_study_material';
                $mediaFile->attachment_file = $fileName;
                $mediaFile->original_name   = $file->getClientOriginalName();
                $mediaFile->file_extension  = $extension;
                $mediaFile->file_size       = $file->getSize();
                $mediaFile->mime_type       = $file->getMimeType();
                $mediaFile->uploaded_by     = Auth::id();
                $mediaFile->save();

                return redirect()->back()->with(['success' => config('constants.FLASH_REC_ADD_1')]);
            } else {
                return redirect()->back()->with(['error' => config('constants.FLASH_REC_ADD_0')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function sendNotification(Request $request)
    {
        $users = User::select('users.id', 'user_additional_details.school_id', 'student_details.class')
            ->join('user_additional_details', 'user_additional_details.user_id', '=', 'users.id')
            ->join('student_details', 'student_details.user_id', '=', 'users.id')
            ->where('users.status', 1)                               // Ensure the status is 1
            ->where('user_additional_details.school_id', Auth::id()) // Ensure the school_id matches Auth::id()
            ->where('student_details.class', $request->class_id)     // Ensure the class matches the requested class_id
            ->get();

        $notifications = [];
        foreach ($users as $user) {
            $notifications[] = [
                'type'            => 'online_class',
                'notifiable_type' => 'App\Models\User',
                'user_id'         => $user->id,
                'from_id'         => Auth::id(),
                'data'            => json_encode([
                    'title'      => $request->title,
                    'class_id'   => $request->class_id,
                    'class_date' => $request->class_date,
                    'start_time' => $request->start_time,
                    'end_time'   => $request->end_time,
                    'join_link'  => $request->join_link,
                ]),
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        // Batch insert notifications
        DB::table('notifications')->insert($notifications);
    }
}
