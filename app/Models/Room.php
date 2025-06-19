<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = ['name'];


    // Relasi ke Schedule
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
    public $timestamps = false;

}
