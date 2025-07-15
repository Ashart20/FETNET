<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = [];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    /**
     * Relasi ke Prodi melalui model Activity.
     */
    public function prodi(): HasOneThrough
    {
        return $this->hasOneThrough(Prodi::class, Activity::class, 'id', 'id', 'activity_id', 'prodi_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(MasterRuangan::class, 'room_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function studentGroups(): BelongsToMany
    {
        return $this->activity->studentGroups();
    }

    /**
     * Relasi many-to-many ke Dosen.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'schedule_teacher');
    }
}
