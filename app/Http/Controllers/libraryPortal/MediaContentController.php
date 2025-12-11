<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CoreController;
use App\Models\AccessCode;
use App\Models\Classes;
use App\Models\MediaFiles;
use App\Models\MediaFolder;
use App\Models\SchoolAssignedDigitalContent;
use App\Models\Subject;
use App\Models\UserAdditionalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaContentController extends Controller
{
    public $data = [];


    public function contentUpload(Request $request)
    {
        $role     = getUserRoles();
        $parentId = Auth::id();

        // If the role is "school_teacher", adjust parentId and use instructor_id for fetching classes
        if ($role == "school_teacher") {
            $parentId = Auth::user()->userAdditionalDetail->school_id;
        }
        // Step 1: Get unique, non-null series IDs assigned to the school
        $schoolAssignedSeries = SchoolAssignedDigitalContent::where('school_id', $parentId)
            ->whereNotNull('series_id')
            ->pluck('series_id')
            ->unique()
            ->toArray();

        // Step 2: Get folders matching assigned series and roles
        // $this->data['mittlearnFolderListing'] = MediaFolder::where('is_mittlearn_folder', 1)
        //     ->where(function ($query) use ($schoolAssignedSeries) {
        //         foreach ($schoolAssignedSeries as $seriesId) {
        //             // Match comma-separated values using FIND_IN_SET
        //             $query->orWhereRaw("FIND_IN_SET(?, distribute_series_ids)", [$seriesId]);
        //         }
        //     })
        //     ->where(function ($query) {
        //         $query->whereRaw("FIND_IN_SET('school_admin', distribute_role_slug)")
        //             ->orWhereRaw("FIND_IN_SET('school_teacher', distribute_role_slug)");
        //     })
        //     ->withCount('fileCount')
        //     ->get();


        // Step 2: Get folders matching assigned series and roles
        $mittlearnFolders = MediaFolder::where('is_mittlearn_folder', 1)
            ->where(function ($query) use ($schoolAssignedSeries) {
                foreach ($schoolAssignedSeries as $seriesId) {
                    $query->orWhereRaw("FIND_IN_SET(?, distribute_series_ids)", [$seriesId]);
                }
            })
            ->where(function ($query) use ($role) {
                $query->whereRaw("FIND_IN_SET('school_admin', distribute_role_slug)")
                    ->orWhereRaw("FIND_IN_SET('school_teacher', distribute_role_slug)");
            })
            ->withCount('fileCount')
            ->get();

        // Filter folder access based on distribute_schools / distribute_teachers rules
        $filteredFolders = $mittlearnFolders->filter(function ($folder) use ($role, $parentId) {

            // STEP 0: Convert CSV strings → arrays
            $allowedSchools  = $folder->distribute_schools === 'all'
                ? 'all'
                : array_filter(explode(',', $folder->distribute_schools));

            $allowedTeachers = $folder->distribute_teachers === 'all'
                ? 'all'
                : array_filter(explode(',', $folder->distribute_teachers));

            // STEP 1 — Check if this role exists in distribute_role_slug
            $allowedRoles = array_filter(explode(',', $folder->distribute_role_slug));

            if (!in_array($role, $allowedRoles)) {
                // This folder is NOT meant for this role → hide it
                return false;
            }

            // ===== CASE 1: SCHOOL ADMIN =====
            if ($role === 'school_admin') {

                // If the folder assigned "all schools"
                if ($allowedSchools === 'all') {
                    return true;
                }

                // Otherwise, admin must belong to the allowed school list
                return in_array($parentId, $allowedSchools);
            }

            // ===== CASE 2: SCHOOL TEACHER =====
            if ($role === 'school_teacher') {

                $teacherId = Auth::id();

                // Teacher's school
                $teacherSchoolId = Auth::user()->userAdditionalDetail->school_id;

                // If all teachers allowed
                if ($allowedTeachers === 'all') {
                    return true;
                }

                // If this specific teacher ID is allowed
                if (in_array($teacherId, $allowedTeachers)) {
                    return true;
                }

                // OPTIONAL CASE: School-based matching
                // If school IDs stored in distribute_schools match teacher's school
                if ($allowedSchools !== 'all' && in_array($teacherSchoolId, $allowedSchools)) {
                    return true;
                }

                return false;
            }

            return false;
        });


        // Assign final visible folders
        $this->data['mittlearnFolderListing'] = $filteredFolders;


        $this->data['folderListing'] = MediaFolder::where('is_mittlearn_folder', 0)->where('parent_id', $parentId)->where('class_id', null)->withCount('fileCount')->get();
        $this->data['teacherFolderListing'] = MediaFolder::where('is_mittlearn_folder', 0)->where('parent_id', $parentId)->whereNotNull('class_id')->withCount('fileCount')->get();
        // dd($this->data['folderListing'],$this->data['teacherFolderListing']);
        $this->data['classCourses']  = AccessCode::with('class')
            ->select('class_id')
            ->where('school_id', $parentId)
            ->groupBy('class_id')
            ->get();

        $teacherAssignedClasses  = getTeacherAssignedClasses();
        $teacherAssignedSubjects = getTeacherAssignedSubjects();

        $this->data['teacherClasses'] = Classes::whereIn('id', $teacherAssignedClasses)->pluck('name', 'id')->toArray();
        $this->data['teacherSubject'] = Subject::whereIn('id', $teacherAssignedSubjects)->pluck('name', 'id')->toArray();

        return view('schoolPortal.mediaContent.content_upload', $this->data);
    }
    public function createFolder(Request $request)
    {
        $role     = getUserRoles();
        $parentId = Auth::id();

        // If the role is "school_teacher", adjust parentId and use instructor_id for fetching classes
        if ($role == "school_teacher") {
            $parentId = Auth::user()->userAdditionalDetail->school_id;
        }
        $request->validate([
            "folder_name"  => "required|string",
            "folder_color" => "required|string",
        ]);
        try {
            $folder               = new MediaFolder;
            $folder->available_to_users  = $request->available_to_users;
            $folder->folder_name  = $request->folder_name;
            $folder->class_id     = $request->class_id;   // class_id coming only if user role is school_teacher
            $folder->subject_id   = $request->subject_id; // subject_id coming only if user role is school_teacher
            $folder->folder_color = $request->folder_color;
            $folder->parent_id    = $parentId;
            $folder->folder_icon  = 'frontend/images/folder-yellow.svg';
            $folder->save();
            // dd($folder);
            if ($folder) {
                return redirect()->back()->with(['success' => config('constants.FLASH_REC_ADD_1')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
        return view('schoolPortal.content_upload');
    }
    public function contentFolderView(Request $request, $id)
    {
        try {
            $this->data['folder'] = MediaFolder::find($id);
            $query                = MediaFiles::where('type', 'content_upload')->where('tbl_id', $id);
            $type                 = $request->query('type');

            if ($type === 'image') {
                $query->whereIn('file_extension', ['jpg', 'jpeg', 'svg', 'png', 'gif', 'webp']);
            }
            if ($type === 'video') {
                $query->whereIn('file_extension', ['mp4', 'avi', 'mov', 'm4v', 'm4p', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'm2v', 'wmv', 'flv', 'mkv', 'webm', '3gp', '3gp', 'm2ts', 'ogv', 'ts', 'mxf']);
            }
            if ($type === 'document') {
                $query->whereIn('file_extension', ['pdf', 'docx', 'xlsx', 'txt', 'pptx', 'csv']);
            }

            $this->data['contentFolderView'] = $query->get();
            return view('schoolPortal.mediaContent.content_folder_view', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function storeFile(Request $request)
    {
        $maxFileSize = config('constants.MAX_FILE_SIZE');

        $request->validate([
            'file' => "required|file|mimes:jpg,jpeg,png,svg,doc,docx,xls,xlsx,pdf,mp3,wav,mp4,mov,avi|max:$maxFileSize",
        ]);

        try {
            $file           = $request->file('file');
            $extension      = $file->getClientOriginalExtension();
            $imageMimeTypes = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'];
            if (in_array($extension, $imageMimeTypes)) {
                $compressedImagePath = storage_path('app/public/uploads/media-files/' . time() . '.' . $extension);
                compressImage($file, $compressedImagePath);

                $fileName = basename($compressedImagePath);
                $path     = 'uploads/media-files/' . $fileName;
            } else {
                $fileName = time() . '.' . $extension;
                $path     = Storage::disk('public')->put('uploads/media-files/' . $fileName, file_get_contents($file));
            }

            if ($path) {
                $mediaFile                  = new MediaFiles();
                $mediaFile->tbl_id          = $request->folder_id;
                $mediaFile->type            = 'content_upload';
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
            // dd($e);
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function contentDelete($id)
    {
        try {
            $folder = MediaFolder::find($id);

            if (! $folder) {
                return redirect()->back()->with('error', 'Folder not found');
            }

            $fileName = MediaFiles::where('tbl_id', $folder->id)->where('type', 'content_upload')->first();
            if ($fileName) {
                if (Storage::disk('public')->exists('uploads/media-files/' . $fileName->attachment_file)) {
                    Storage::disk('public')->delete('uploads/media-files/' . $fileName->attachment_file);
                }
                $fileName->delete();
            }
            $folder->delete();
            return redirect()->back()->with(['success' => config('constants.FLASH_REC_DELETE_1')]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }
    public function classContentFolder()
    {
        return view('schoolPortal.class_content_folder');
    }

    public function fileDelete($id)
    {
        try {
            $file = MediaFiles::where('id', $id)->where('type', 'content_upload')->first();

            if ($file) {
                if (Storage::disk('public')->exists('uploads/media-files/' . $file->attachment_file)) {
                    Storage::disk('public')->delete('uploads/media-files/' . $file->attachment_file);
                }
                $file->delete();

                return redirect()->back()->with(['success' => config('constants.FLASH_REC_DELETE_1')]);
            } else {
                return redirect()->back()->with(['error' => config('constants.FLASH_REC_DELETE_0')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', config('constants.FLASH_TRY_CATCH'));
        }
    }
}
