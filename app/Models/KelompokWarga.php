<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class KelompokWarga extends Model
{
    protected $table = 'kelompok_warga';
    protected $primaryKey = 'kelompok_warga_id';
    protected $guarded = [];

    public function members(): HasMany
    {
        return $this->hasMany(KelompokWargaMember::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function representativeMember(): HasOne
    {
        return $this->hasOne(KelompokWargaMember::class, 'kelompok_warga_id', 'kelompok_warga_id')
            ->where('is_perwakilan', true);
    }

    public function representative(): HasOneThrough
    {
        return $this->hasOneThrough(
            Warga::class,
            KelompokWargaMember::class,
            'kelompok_warga_id',
            'warga_id',
            'kelompok_warga_id',
            'warga_id'
        )->where('kelompok_warga_member.is_perwakilan', true);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(WeeklyAvailability::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function getNomorWaPerwakilanAttribute(): ?string
    {
        if ($this->relationLoaded('representativeMember')) {
            return $this->representativeMember?->nomor_wa;
        }

        return $this->representativeMember()->first()?->nomor_wa;
    }
}
