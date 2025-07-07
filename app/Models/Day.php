<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Nonaktifkan timestamp (created_at, updated_at) untuk model ini.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Satu hari bisa memiliki banyak jadwal (schedule).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
