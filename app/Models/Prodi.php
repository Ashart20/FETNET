<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Prodi extends Model
{
    use HasFactory;

    protected $fillable = ['nama_prodi', 'kode', 'cluster_id'];

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class);
    }
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'prodi_teacher');
    }

}
