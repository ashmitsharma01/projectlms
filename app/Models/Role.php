<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['role_name', 'description', 'is_active','role_slug'];

    public function users()
    {
        return $this->hasMany(UserRole::class, 'role_slug', 'role_slug');
    }

}
