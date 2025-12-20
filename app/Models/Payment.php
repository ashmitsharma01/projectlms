<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'library_id',
        'student_user_id',
        'amount',
        'start_date',
        'end_date',
        'payment_date',
        'mode',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    /* Relationships */
    public function user()
    {
        return $this->belongsTo(User::class, 'student_user_id')->with('student');
    }

    public function library()
    {
        return $this->belongsTo(Library::class, 'library_id');
    }
}
