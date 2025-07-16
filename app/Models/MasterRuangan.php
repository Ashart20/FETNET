<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\RoomTimeConstraint;
use Illuminate\Database\Eloquent\Casts\Attribute;


class MasterRuangan extends Model
{
    use HasFactory;

    /**
     * Mendefinisikan nama tabel secara eksplisit.
     * Berguna jika nama model tidak mengikuti konvensi plural Laravel (cth: Ruangan -> ruangans).
     *
     * @var string
     */
    protected $table = 'master_ruangans';

    /**
     * Kolom yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_ruangan',
        'kode_ruangan',
        'building_id',
        'lantai',
        'kapasitas',
        'user_id',
        'tipe',
    ];

    /**
     * Relasi ke model Building.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
    public function timeConstraints(): HasMany
    {
        return $this->hasMany(RoomTimeConstraint::class);
    }    /**
     * Relasi ke model User (Penanggung Jawab).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Activity dimana ruangan ini menjadi preferensi.
     */
    public function preferredForActivities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_preferred_room');
    }
    protected function nameWithCapacity(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->nama_ruangan} (Kapasitas: {$this->kapasitas})",
        );
    }
}
