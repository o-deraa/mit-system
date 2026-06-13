<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Warga extends Model
{
    protected $table = 'warga';
    protected $primaryKey = 'warga_id';
    protected $guarded = [];

    public function representedGroup(): HasOne
    {
        return $this->hasOne(KelompokWarga::class, 'warga_id', 'warga_id');
    }

    public function membership(): HasOne
    {
        return $this->hasOne(KelompokWargaMember::class, 'warga_id', 'warga_id');
    }

    public function groupMembership()
    {
        return $this->hasOne(KelompokWargaMember::class, 'warga_id', 'warga_id');
    }
}
