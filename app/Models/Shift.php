<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'status',
    ];

    public function seatAssignment()
    {
        return $this->hasMany(SeatAssignment::class, 'shift_id', 'id');
    }
}
