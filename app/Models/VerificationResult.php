<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationResult extends Model
{
    protected $table = 'verification_result';
    protected $primaryKey = 'verification_id';
    protected $guarded = [];

    public function maba(): BelongsTo
    {
        return $this->belongsTo(Maba::class, 'maba_id', 'maba_id');
    }

    public function realisasi(): BelongsTo
    {
        return $this->belongsTo(Realisasi::class, 'realisasi_id', 'realisasi_id');
    }
    
    public function week(): BelongsTo
    {
        return $this->belongsTo(MitWeek::class, 'week_id', 'week_id');
    }
}
