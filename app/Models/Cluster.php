<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cluster extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'user_id',
    ];

    /**
     * Mendefinisikan relasi "satu-ke-banyak" ke model Prodi.
     * Satu cluster bisa memiliki BANYAK prodi.
     */
    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
