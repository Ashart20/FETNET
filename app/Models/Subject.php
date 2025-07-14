<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_matkul',
        'kode_matkul',
        'sks',
        'semester',
        'prodi_id',
        'comments',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
    public function getKodeNameAttribute(): string
    {
        return " {$this->nama_matkul} - {$this->kode_matkul} - {$this->semester}";
    }
}
