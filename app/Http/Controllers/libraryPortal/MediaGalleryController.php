<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CoreController;
use App\Models\AccessCode;
use App\Models\Classes;
use App\Models\MediaFiles;
use App\Models\MediaFolder;
use App\Models\MediaGallery;
use App\Models\Subject;
use App\Models\UserAdditionalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaGalleryController extends Controller
{
    public $data = [];

    public function mediaGalleryList(Request $request)
    {
        $role                   = getUserRoles();
        $parentId               = Auth::id();

        if ($role == "school_teacher") {
            $parentId               = Auth::user()->userAdditionalDetail->school_id;
            $this->data['mediaGallery'] = MediaGallery::where('parent_id', $parentId)->whereIn('available_to_users', ['all', 'teachers'])->get();
        } else {
            $this->data['mediaGallery'] = MediaGallery::where('parent_id', $parentId)->get();
        }
        return view('schoolPortal.mediaGallery.media_gallery_list', $this->data);
    }
    public function createMediaGallery(Request $request)
    {
        $request->validate([
            'gallery_name' => 'required',
            'available_to_users' => 'required',
            'event_name' => 'required',
            'event_name' => 'required',
        ]);

        $maxFileSize = config('constants.MAX_FILE_SIZE');

        $request->validate([
            'media_file' => "required|file|mimes:jpg,jpeg,png,svg|max:$maxFileSize",
        ]);
        $maxFileSize = config('constants.MAX_FILE_SIZE');

        if ($request->id > 0) {
            $success = config('constants.FLASH_REC_UPDATE_1');
            $error = config('constants.FLASH_REC_UPDATE_0');
        } else {
            $success = config('constants.FLASH_REC_ADD_1');
            $error = config('constants.FLASH_REC_ADD_0');
        }

        $res = MediaGallery::updateOrCreate(['id' => $request->id], ['parent_id' => Auth::id(), 'gallery_name' => $request->gallery_name, 'available_to_users' => $request->available_to_users, 'event_name' => $request->event_name, 'media_link' => $request->media_link, 'description' => $request->description, 'validity_date' => $request->validity_date]);

        if ($request->hasFile('media_file')) {

            $file           = $request->file('media_file');
            $extension      = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $path     = Storage::disk('public')->put('uploads/media-gallery/' . $fileName, file_get_contents($file));

            if ($path) {
                $mediaFile                  = new MediaFiles();
                $mediaFile->tbl_id          = $res->id;
                $mediaFile->type            = 'school_media_gallery';
                $mediaFile->attachment_file = $fileName;
                $mediaFile->original_name   = $file->getClientOriginalName();
                $mediaFile->file_extension  = $extension;
                $mediaFile->file_size       = $file->getSize();
                $mediaFile->mime_type       = $file->getMimeType();
                $mediaFile->uploaded_by     = Auth::id();
                $mediaFile->save();
            }

            if ($res) {
                return redirect()->route('gallery.list')->with(['success' => $success]);
            }
        }
        return redirect()->back()->with(['error' => $error]);
    }

    public function mediaGalleryView(Request $request, $id)
    {
        try {
            $this->data['role']                   = getUserRoles();

            $this->data['mediaGallery'] = MediaGallery::find($id);
            $query                = MediaFiles::where('type', 'school_media_gallery')->where('tbl_id', $id);
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

            $this->data['mediaGalleryView'] = $query->get();
            return view('schoolPortal.mediaGallery.media_folder_view', $this->data);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }
    public function mediaGalleryDetele(Request $request, $id)
    {
        try {
            $folder = MediaGallery::find($id);

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
    public function storeFile(Request $request)
    {
        $maxFileSize = config('constants.MAX_FILE_SIZE');

        $request->validate([
            'file' => "required|file|mimes:jpg,jpeg,png,svg|max:$maxFileSize",
        ]);

        try {
            if ($request->hasFile('file')) {
                $file           = $request->file('file');
                $extension      = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $extension;
                $path     = Storage::disk('public')->put('uploads/media-gallery/' . $fileName, file_get_contents($file));

                if ($path) {
                    $mediaFile                  = new MediaFiles();
                    $mediaFile->tbl_id          = $request->id;
                    $mediaFile->type            = 'school_media_gallery';
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
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => config('constants.FLASH_TRY_CATCH')]);
        }
    }

    public function fileDelete($id)
    {
        try {
            $file = MediaFiles::where('id', $id)->where('type', 'school_media_gallery')->first();

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
