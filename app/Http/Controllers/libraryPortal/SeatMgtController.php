<?php

namespace App\Http\Controllers\libraryPortal;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Seat;
use App\Models\SeatAssignment;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeatMgtController extends Controller
{
    public $data = [];

    public function index(Request $request)
    {
        $library_id               = Library::where('user_id', Auth::id())->value('id');
        $query = Seat::where('library_id', $library_id)
            ->with([
                'fullDay.user',
                'firstHalf.user',
                'secondHalf.user'
            ])
            ->orderBy('created_at', 'ASC');

        $this->data['seats'] = $query->get();
        $this->data['users'] = User::whereHas('role', function ($q) {
            $q->where('role_slug', 'student');
        })
            ->whereHas('student', function ($q) use ($library_id) {
                $q->where('library_id', $library_id);
            })
            ->whereDoesntHave('seatAssignment')
            ->pluck('name', 'id');
        return view('libraryPortal.seats.index', $this->data);
    }

    public function seatAssign(Request $request)
    {
        try {
            $request->validate([
                'seat_id'    => 'required|exists:seats,id',
                'user_id'    => 'required|exists:users,id',
                'shift'      => 'required|in:full_day,first_half,second_half',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after_or_equal:start_date',
            ]);

            $libraryId = Auth::id();

            $shiftMap = [
                'full_day'    => 'Full Day',
                'first_half'  => 'First Half',
                'second_half' => 'Second Half',
            ];

            $shift = Shift::where('name', $shiftMap[$request->shift])->first();

            if (!$shift) {
                return redirect()->back()->with('error', 'Shift not found');
            }

            $alreadyAssigned = SeatAssignment::where([
                'seat_id'  => $request->seat_id,
                'shift_id' => $shift->id,
                'status'   => 1,
            ])
                ->whereDate('end_date', '>=', now())
                ->exists();

            if ($alreadyAssigned) {
                return redirect()->back()->with('error', 'Seat already assigned for this shift');
            }

            SeatAssignment::updateOrCreate(
                [
                    'seat_id'  => $request->seat_id,
                    'shift_id' => $shift->id,
                    'status'   => 1
                ],
                [
                    'library_id' => $libraryId,
                    'user_id'    => $request->user_id,
                    'start_date' => $request->start_date,
                    'end_date'   => $request->end_date,
                ]
            );

            $this->updateSeatStatus($request->seat_id);
            return redirect()->back()->with('success', 'Seat assigned successfully');
        } catch (ValidationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', config('constants.FLASH_TRY_CATCH'));
        }
    }


    private function updateSeatStatus($seatId)
    {
        $activeAssignments = SeatAssignment::where('seat_id', $seatId)
            ->where('status', 1)
            ->whereDate('end_date', '>=', now())
            ->count();

        Seat::where('id', $seatId)->update([
            'status' => $activeAssignments > 0 ? 'booked' : 'available'
        ]);
    }

    public function deleteSeatAssignment($id)
    {
        try {
            $assignment = SeatAssignment::findOrFail($id);
            $seatId = $assignment->seat_id;

            $assignment->delete();

            $this->updateSeatStatus($seatId);

            return redirect()->back()
                ->with('success', 'Seat assignment removed successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to delete seat assignment');
        }
    }
}
