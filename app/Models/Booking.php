<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $table = 'booking';
    protected $primaryKey = 'booking_id';
    protected $guarded = [];

    public function group(): BelongsTo
    {
        return $this->belongsTo(KelompokWarga::class, 'kelompok_warga_id', 'kelompok_warga_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Maba::class, 'created_by_maba_id', 'maba_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class, 'booking_id', 'booking_id');
    }

    public function realisasi(): HasOne
    {
        return $this->hasOne(Realisasi::class, 'booking_id', 'booking_id');
    }

    public function week()
    {
        return $this->belongsTo(MitWeek::class, 'week_id', 'week_id');
    }
}
