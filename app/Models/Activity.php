<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        return $this->belongsToMany(Teacher::class, 'activity_teacher')
            ->withTimestamps() // Penting untuk memberitahu Eloquent agar mengelola timestamps
            ->orderBy('activity_teacher.created_at', 'asc'); // Mengurutkan berdasarkan waktu pembuatan
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

    public function getNameOrSubjectAttribute(): string
    {
        return $this->name ?? $this->subject?->nama_matkul ?? 'Tanpa Nama';
    }
}
