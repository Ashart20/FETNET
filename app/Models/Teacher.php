<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_dosen',
        'kode_dosen',
        'title_depan',
        'title_belakang',
        'kode_univ',
        'employee_id',
        'email',
        'nomor_hp',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    protected function fullname(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->title_depan.' '.$this->nama_dosen.' '.$this->title_belakang)
        );
    }

    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'prodi_teacher');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_teacher');
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'schedule_teacher');
    }
}
