<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{

    protected $table = 'states';
    protected $fillable = [
        'name',
        'country_id',
    ];
    public $timestamps = false;


    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
