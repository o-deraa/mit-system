<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Maba;
use App\Models\MitWeek;

class Realisasi extends Model
{
    protected $table = 'realisasi';
    protected $primaryKey = 'realisasi_id';
    protected $guarded = [];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function verificationResults(): HasMany
    {
        return $this->hasMany(VerificationResult::class, 'realisasi_id', 'realisasi_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(Maba::class, 'submitted_by_maba_id', 'maba_id');
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(MitWeek::class, 'week_id', 'week_id');
    }
}
