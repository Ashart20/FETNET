<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'subject',
        'teacher',
        'room_id',
        'time_slot_id',
        'duration',
        'activity_id',
        'students_group'
    ];

    // Relasi ke Room
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // Relasi ke TimeSlot
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
