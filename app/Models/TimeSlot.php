<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_time',
        'end_time',
    ];

    /**
     * Nonaktifkan timestamp (created_at, updated_at) untuk model ini.
     */
    public $timestamps = false;

    /**
     * Satu slot waktu bisa digunakan di banyak jadwal.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Satu slot waktu bisa menjadi batasan di banyak constraint ruangan.
     */
    public function roomTimeConstraints(): HasMany
    {
        return $this->hasMany(RoomTimeConstraint::class);
    }

    /**
     * Satu slot waktu bisa menjadi batasan di banyak constraint dosen.
     */
    public function teacherTimeConstraints(): HasMany
    {
        return $this->hasMany(TeacherTimeConstraint::class);
    }
}
