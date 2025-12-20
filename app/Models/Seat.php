<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'library_id',
        'seat_number',
        'status',
    ];

    public function library()
    {
        return $this->belongsTo(Library::class, 'library_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(SeatAssignment::class, 'seat_id');
    }

    // Full day
    public function fullDay()
    {
        return $this->hasOne(SeatAssignment::class, 'seat_id')
            ->whereHas('shift', fn($q) => $q->where('name', 'Full Day'))
            ->where('status', 1);
    }

    // First half
    public function firstHalf()
    {
        return $this->hasOne(SeatAssignment::class, 'seat_id')
            ->whereHas('shift', fn($q) => $q->where('name', 'First Half'))
            ->where('status', 1);
    }

    // Second half
    public function secondHalf()
    {
        return $this->hasOne(SeatAssignment::class, 'seat_id')
            ->whereHas('shift', fn($q) => $q->where('name', 'Second Half'))
            ->where('status', 1);
    }

    // UI color
    public function getStatusClassAttribute()
    {
        if ($this->fullDay) return 'seat-green';
        if ($this->firstHalf || $this->secondHalf) return 'seat-yellow';
        return 'seat-white';
    }
}
