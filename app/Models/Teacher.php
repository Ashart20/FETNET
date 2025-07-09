<?php

namespace App\Models;

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
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }
    public function prodis(): BelongsToMany
    {
        return $this->belongsToMany(Prodi::class, 'prodi_teacher');
    }
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_teacher');
    }
}
