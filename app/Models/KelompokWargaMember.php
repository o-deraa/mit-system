<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokWargaMember extends Model
{
    protected $table = 'kelompok_warga_member';
    protected $primaryKey = 'member_id';
    protected $guarded = [];

    protected $casts = [
        'is_perwakilan' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(KelompokWarga::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id', 'warga_id');
    }
}
