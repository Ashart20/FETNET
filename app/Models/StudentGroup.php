<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelompok',
        'kode_kelompok',
        'jumlah_mahasiswa',
        'prodi_id',
        'angkatan',
        'parent_id',
    ];

    /**
     * Mendefinisikan relasi ke parent group.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(StudentGroup::class, 'parent_id');
    }

    /**
     * Mendefinisikan relasi ke children group (satu level).
     */
    public function children(): HasMany
    {
        return $this->hasMany(StudentGroup::class, 'parent_id');
    }

    /**
     * Relasi rekursif untuk memuat semua turunan (child) secara berulang.
     * Ini yang digunakan oleh FetFileGeneratorService.
     */
    public function childrenRecursive(): HasMany
    {
        return $this->hasMany(StudentGroup::class, 'parent_id')->with('childrenRecursive');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
}
