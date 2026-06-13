<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokWarga extends Model
{
    protected $table = 'kelompok_warga';
    protected $primaryKey = 'kelompok_warga_id';
    protected $guarded = [];

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'warga_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(KelompokWargaMember::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(WeeklyAvailability::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }
}
