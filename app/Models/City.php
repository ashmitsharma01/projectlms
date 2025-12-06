<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    protected $table = 'cities';
    protected $fillable = [
        'city',
        'state_id',
    ];
    public $timestamps = false;
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
