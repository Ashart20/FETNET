<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    /**
     * Sebuah Gedung memiliki banyak Ruangan.
     */
    public function rooms(): HasMany
    {
        // 'building_id' adalah foreign key di tabel 'master_ruangans'
        return $this->hasMany(MasterRuangan::class);
    }
}
