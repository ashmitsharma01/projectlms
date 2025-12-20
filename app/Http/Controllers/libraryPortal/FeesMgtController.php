<?php

namespace App\Http\Controllers\libraryPortal;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FeesMgtController extends Controller
{
    public $data = [];

    public function dashboard(Request $request)
    {
        return view('libraryPortal.feesMgt.dashboard', $this->data);
    }
    public function collectFees(Request $request)
    {
        $libraryID = Library::where('user_id', Auth::id())->value('id');
        $this->data['students'] = User::whereHas('role', function ($q) {
            $q->where('role_slug', 'student');
        })->whereHas('student', function ($q) use ($libraryID) {
            $q->where('library_id', $libraryID);
        })->pluck('name', 'id');

        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        $this->data['userPaidFeesList'] = Payment::with(['user'])
            ->where('library_id', $libraryID)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->latest('payment_date')
            ->get();
        // dd($this->data['userPaidFeesList']);

        return view('libraryPortal.feesMgt.collect-fees', $this->data);
    }
    public function editCollectFees($paymentId)
    {
        $libraryID = Library::where('user_id', Auth::id())->value('id');
        $this->data['students'] = User::whereHas('role', function ($q) {
            $q->where('role_slug', 'student');
        })->whereHas('student', function ($q) use ($libraryID) {
            $q->where('library_id', $libraryID);
        })->pluck('name', 'id');


        $this->data['payment'] = Payment::with('user')->where('id', $paymentId)
        ->where('library_id', $libraryID)
        ->firstOrFail();

        return view('libraryPortal.feesMgt.edit-collect-fees', $this->data);
    }

    public function collectFeesSave(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'student_id'   => 'required|exists:users,id',
                'start_date'   => 'required|date',
                'end_date'     => 'required|date|after_or_equal:start_date',
                'amount'       => 'required|numeric|min:0',
                'payment_mode' => 'required|in:cash,upi,card',
            ]);

            $library = Library::where('user_id', Auth::id())->firstOrFail();
            if ($request->student_id) {
                $student = Student::where('user_id', $request->student_id)->first();
                if ($student && $student->is_new == 1) {
                    $student->update([
                        'is_new' => 0
                    ]);
                } elseif ($student && $student->is_renew == 1) {
                    $student->update([
                        'is_renew' => 0
                    ]);
                }
            }

            Payment::updateOrCreate(
                [
                    'id' => $request->payment_id,
                ],
                [
                    'library_id'   => $library->id,
                    'student_user_id'  => $request->student_id,
                    'amount'       => $request->amount,
                    'start_date'   => $request->start_date,
                    'end_date'     => $request->end_date,
                    'payment_date' => now()->toDateString(),
                    'mode'         => $request->payment_mode,
                ]
            );
            DB::commit();
            if ($request->isFromStudentManagment == 1) {
                return redirect()
                    ->route('student.manager')
                    ->with('success', $request->payment_id ? 'Fees updated successfully' : 'Fees collected successfully');
            }
            return redirect()
                ->route('collect.fees')
                ->with('success', $request->payment_id ? 'Fees updated successfully' : 'Fees collected successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', config('constants.FLASH_TRY_CATCH'));
        }
    }

    public function userPaymentHistory(Request $request)
    {
        $libraryID = Library::where('user_id', Auth::id())->value('id');
        $this->data['students'] = User::whereHas('role', function ($q) {
            $q->where('role_slug', 'student');
        })->whereHas('student', function ($q) use ($libraryID) {
            $q->where('library_id', $libraryID);
        })->pluck('name', 'id');


        $studentID = $request->student_id ?? null;

        $this->data['userPaymentHistory'] = Payment::with(['user'])
            ->where('library_id', $libraryID)
            ->where('student_user_id', $studentID)
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($this->data['userPaidFeesList']);

        return view('libraryPortal.feesMgt.user-payment-history', $this->data);
    }




    // private function updateSeatStatus($seatId)
    // {
    //     $activeAssignments = SeatAssignment::where('seat_id', $seatId)
    //         ->where('status', 1)
    //         ->whereDate('end_date', '>=', now())
    //         ->count();

    //     Seat::where('id', $seatId)->update([
    //         'status' => $activeAssignments > 0 ? 'booked' : 'available'
    //     ]);
    // }

    // public function deleteSeatAssignment($id)
    // {
    //     try {
    //         $assignment = SeatAssignment::findOrFail($id);
    //         $seatId = $assignment->seat_id;

    //         $assignment->delete();

    //         $this->updateSeatStatus($seatId);

    //         return redirect()->back()
    //             ->with('success', 'Seat assignment removed successfully');
    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->with('error', 'Unable to delete seat assignment');
    //     }
    // }
}
