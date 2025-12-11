<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Api\BaseController;
use App\Models\City;
use App\Models\Library;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LibraryApiController extends BaseController
{
    public $data     = [];
    public $res      = [];


    public function librarySave(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'address'  => 'required|string',
                'city'     => 'required|string|max:100',
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

            if ($request->city) {
                $city = City::where('id', $request->city)->value('city');
            }
            if($request->id){
                $existingLibraryCode = Library::where('user_id',$request->id)->value('library_code');
            }
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
                    'status'   => $request->status,
                    'total_seats'   => $request->total_seats,
                    'library_code' => $request->id
                        ? ($existingLibraryCode ?? null)
                        : $this->generateLibraryCode($request->name, $city),
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

    public function getLibraries(Request $request)
    {
        try {

            $libraries = Library::with(['user', 'userRole'])->get();

            return response()->json([
                'status'  => true,
                'message' => 'Library fetched successfully.',
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

    private function generateLibraryCode($libraryName, $cityName)
    {
        // Words to ignore
        $wordsToIgnore = ['the', 'of'];

        // Split library name into words
        $words = explode(' ', strtolower($libraryName));

        // Filter meaningful words
        $filtered = array_filter($words, fn($word) => !in_array($word, $wordsToIgnore));

        // Take first letter of each word, up to 3 letters
        $prefix = '';
        $i = 0;
        foreach ($filtered as $word) {
            $prefix .= strtoupper(substr($word, 0, 1));
            $i++;
            if ($i >= 3) break; // max 3 letters
        }

        if (!$prefix) $prefix = 'LIB'; // fallback

        // STEP 1: check if code exists
        if (!Library::where('library_code', $prefix)->exists()) {
            return $prefix;
        }

        // STEP 2: add first letter of city
        $cityLetter = strtoupper(substr($cityName, 0, 1));
        $prefixWithCity = $prefix . $cityLetter;

        if (!Library::where('library_code', $prefixWithCity)->exists()) {
            return $prefixWithCity;
        }

        // STEP 3: add random number 1-9 if still exists
        return $prefixWithCity . rand(1, 9);
    }
}
