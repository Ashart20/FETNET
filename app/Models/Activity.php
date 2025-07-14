<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi many-to-many ke Teacher.
     * Satu aktivitas bisa diajar oleh BANYAK dosen.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'activity_teacher');
    }

    /**
     * Relasi many-to-many ke MasterRuangan (untuk preferensi ruangan).
     */
    public function preferredRooms(): BelongsToMany
    {
        return $this->belongsToMany(MasterRuangan::class, 'activity_preferred_room');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function studentGroups(): BelongsToMany
    {

        return $this->belongsToMany(StudentGroup::class, 'activity_student_group');
    }

    public function activityTag(): BelongsTo
    {
        return $this->belongsTo(ActivityTag::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function getTeacherNamesAttribute(): string
    {
        return $this->teachers->pluck('nama_dosen')->implode(', ');
    }
}
