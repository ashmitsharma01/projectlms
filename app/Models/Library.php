<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Library extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'admin_id',
        'user_id',
        'library_code',
        'name',
        'total_seats',
        'address',
        'city',
        'state',
        'pincode',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function userRole()
    {
        return $this->hasOne(UserRole::class, 'user_id', 'user_id');
    }
}
