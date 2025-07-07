<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomTimeConstraint extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_ruangan_id',
        'day_id',
        'time_slot_id',
    ];

    /**
     * Relasi ke Ruangan yang dibatasi.
     */
    public function masterRuangan(): BelongsTo
    {
        return $this->belongsTo(MasterRuangan::class);
    }

    /**
     * Relasi ke Hari dimana batasan berlaku.
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Relasi ke Slot Waktu dimana batasan berlaku.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
