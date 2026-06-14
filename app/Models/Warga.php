<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Warga extends Model
{
    protected $table = 'warga';
    protected $primaryKey = 'warga_id';
    protected $guarded = [];

    public function representedGroup(): HasOneThrough
    {
        return $this->hasOneThrough(
            KelompokWarga::class,
            KelompokWargaMember::class,
            'warga_id',
            'kelompok_warga_id',
            'warga_id',
            'kelompok_warga_id'
        )->where('kelompok_warga_member.is_perwakilan', true);
    }

    public function membership(): HasOne
    {
        return $this->hasOne(KelompokWargaMember::class, 'warga_id', 'warga_id');
    }

    public function groupMembership(): HasOne
    {
        return $this->hasOne(KelompokWargaMember::class, 'warga_id', 'warga_id');
    }
}
