<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    protected $fillable = ['day', 'start_time', 'end_time'];

    // Relasi ke Schedule
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
    public $timestamps = false;

}
