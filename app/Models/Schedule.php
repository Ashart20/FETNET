<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'activity_id', 'kode_mk', 'subject', 'kode_dosen', 'teacher',
        'kelas', 'sks', 'room_id', 'time_slot_id' // <- jumlah_peserta dihapus
    ];

    /**
     * Get the room associated with the schedule.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the time slot associated with the schedule.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Optional helper: get day from timeSlot directly
     */
    public function getDayAttribute(): ?string
    {
        return $this->timeSlot?->day;
    }

    /**
     * Optional helper: get time string from timeSlot
     */
    public function getTimeRangeAttribute(): ?string
    {
        return $this->timeSlot
            ? $this->timeSlot->start_time . ' - ' . $this->timeSlot->end_time
            : null;
    }

    /**
     * Optional helper: get room name
     */
    public function getRoomNameAttribute(): ?string
    {
        return $this->room?->name;
    }
}
