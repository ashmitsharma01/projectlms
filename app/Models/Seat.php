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
        return $this->hasMany(SeatAssignment::class, 'seat_id', 'id');
    }
}
