<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\SeatAssignment;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Models\User;

class CheckSeatExpiry extends Command
{
    protected $signature = 'seats:check-expiry';

    protected $description = 'Mark students as renew required if seat assignment is expired';

    public function handle()
    {
        $today = Carbon::today();

        // Get expired seat assignments
        $expiredSeats = SeatAssignment::whereDate('end_date', '<', $today)->get();

        foreach ($expiredSeats as $seat) {

            $student = Student::where('user_id', $seat->user_id)->first();

            if ($student && $student->is_renew == 0) {
                $student->update([
                    'is_renew' => 1
                ]);
                $owner = User::find($seat->library_id);
                // 3️⃣ Send in-app notification
                if ($owner) {
                    // 🔒 3️⃣ CHECK IF ALREADY NOTIFIED (ADD THIS HERE)
                    $alreadyNotified = $owner->notifications()
                        ->where('data->student_id', $student->user_id)
                        ->where('data->title', 'Fees Expired')
                        ->exists();
                    if (! $alreadyNotified) {
                        NotificationService::send($owner, [
                            'title' => 'Fees Expired',
                            'message' => 'Fees expired for student: ' . $student->name,
                            'end_date' => $seat->end_date,
                            'url' => route('student.manager')
                        ]);
                    }
                }


                // optional logging
                Log::info("Seat expired → Renew flag set", [
                    'user_id' => $seat->user_id,
                    'end_date' => $seat->end_date
                ]);
            }
        }

        $this->info('Seat expiry check completed.');
    }
}




