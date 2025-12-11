<?php

namespace App\Http\Controllers\schoolPortal;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Schools;
use App\Models\State;
use App\Models\User;
use App\Models\UserAdditionalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SchoolPortalProfileController extends Controller
{
    public $data = [];
    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            $user = auth()->user();
            $storage = Storage::disk('public');
            if ($user->image && $storage->exists('uploads/user/profile_image/' . $user->image)) {
                $storage->delete('uploads/user/profile_image/' . $user->image);
            }

            $file = $request->file('profile_image');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $path     = 'uploads/user/profile_image/' . $filename;
            Storage::disk('public')->put($path, file_get_contents($file));
            $user->image = $filename;
            $user->save();

            return response()->json([
                'success' => true,
                'filePath' => asset('storage/' . $path),
                'message' => 'Profile image updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'password'    => 'required',                 // Current password
            'newpassword' => 'required|min:8|confirmed', // New password with confirmation
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Verify the current password
        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The current password is incorrect.']);
        }

        // Check if the new password is the same as the old password
        if (Hash::check($request->newpassword, $user->password)) {
            return back()->withErrors(['newpassword' => 'The new password cannot be the same as the current password.']);
        }

        // Update the user's password
        $user->password = Hash::make($request->newpassword);
        $user->validate_string = $request->newpassword;
        $user->save();

        // Return success response
        return back()->with('success', 'Password successfully changed!');
    }

    public function updateProfileDetails(Request $request)
    {
        try {
            $role = getUserRoles();
            // If the role is "school_teacher", adjust update data accordingly
            if ($role == "school_teacher") {
                $validated = $request->validate([
                    'id'            => 'required|exists:users,id',
                    'name'          => 'required|string|max:255',
                    'gender'        => 'nullable|string',
                    'experience'    => 'min:0',
                    'qualification' => 'nullable|string|max:255',
                    'dob'           => 'nullable|date',
                    'age'           => 'nullable|numeric|min:0|max:150',
                ]);
                // Update the User table
                $user       = User::findOrFail($validated['id']);
                $user->name = $validated['name'];
                $user->save();

                // Update the UserAdditionalDetail table
                $userAdditionalDetail = $user->userAdditionalDetail;
                if ($userAdditionalDetail) {
                    $userAdditionalDetail->gender        = $validated['gender'];
                    $userAdditionalDetail->experience    = $validated['experience'];
                    $userAdditionalDetail->qualification = $validated['qualification'];
                    $userAdditionalDetail->dob           = $validated['dob'];
                    $userAdditionalDetail->age           = $validated['age'];
                    $userAdditionalDetail->save();
                }
            } else {
                $request->validate([
                    // 'website' => 'nullable|url',
                    'decision_maker'           => 'required|string|max:255',
                    'decision_maker_mobile_no' => 'required|numeric|digits:10',
                    // 'school_board' => 'required|integer|exists:school_boards,id',
                    // 'school_medium' => 'required|array',
                    // 'school_medium.*' => 'integer|exists:school_mediums,id',

                ]);
                $userId        = $request->input('id');
                $user = User::find($userId);
                if ($user) {
                    $user->mobile_no = $request->decision_maker_mobile_no;
                    $user->update();
                }

                $schoolDetails = UserAdditionalDetail::where('user_id', $userId)->first();
                if ($schoolDetails) {
                    $schoolDetails->website                  = $request->website;
                    $schoolDetails->decision_maker           = $request->decision_maker;
                    $schoolDetails->decision_maker_mobile_no = $request->decision_maker_mobile_no;
                    $schoolDetails->save();
                }
            }
            return redirect()->back()->with('success', 'Your Data updated successfully');
        } catch (\TypeError $e) {
            return redirect()->back()->with('error', 'A type error occurred while updating your data. Please try again.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating your data. Please try again.');
        }
    }
    public function updateProfileAddress(Request $request)
    {
        $role = getUserRoles();
        if ($role == "school_teacher") {
            $validated = $request->validate([
                'id'      => 'required|exists:users,id',
                'address' => 'required|string|max:500',
                'country' => 'required|string',
                'state'   => 'required|exists:states,id',
                'city'    => 'required|exists:cities,id',
            ]);
        } else {
            $validated = $request->validate([
                // 'website' => 'nullable|url',
                'postal_code' => 'required|string|max:20',
                'state'       => 'required|string|max:255',
                'city'        => 'required|string|max:255',
                'address'     => 'required|string|max:255',
            ]);
        }
        try {
            // If the role is "school_teacher", adjust update data accordingly
            if ($role == "school_teacher") {
                // Find the user
                $user = User::findOrFail($validated['id']);

                // Check if the user has additional details
                $userAdditionalDetail = $user->userAdditionalDetail;

                if ($userAdditionalDetail) {
                    // Update the user's address details
                    $userAdditionalDetail->address = $validated['address'];
                    $userAdditionalDetail->country = $validated['country'];
                    $userAdditionalDetail->state   = $validated['state'];
                    $userAdditionalDetail->city    = $validated['city'];
                    $userAdditionalDetail->save();
                }
            } else {

                $userId        = $request->input('id');
                $user = User::findOrFail($userId);

                $userAdditionalDetail = $user->userAdditionalDetail;
                $userAdditionalDetail->state   = $validated['state'];
                $userAdditionalDetail->city    = $validated['city'];
                $userAdditionalDetail->save();

                $schoolDetails = Schools::where('user_id', $userId)->first();
                if ($schoolDetails) {
                    $schoolDetails->postal_code = $request->postal_code;
                    $schoolDetails->state       = $request->state;
                    $schoolDetails->city        = $request->city;
                    $schoolDetails->address     = $request->address; // Convert array to comma-separated string
                    $schoolDetails->save();
                }
            }
            return redirect()->back()->with('success', 'Your Data updated successfully');
        } catch (\TypeError $e) {
            return redirect()->back()->with('error', 'A type error occurred while updating your data. Please try again.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating your data. Please try again.');
        }
    }
}
