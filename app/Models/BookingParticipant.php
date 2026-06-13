<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingParticipant extends Model
{
    protected $table = 'booking_participant';
    protected $primaryKey = 'booking_participant_id';
    protected $guarded = [];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function maba(): BelongsTo
    {
        return $this->belongsTo(Maba::class, 'maba_id', 'maba_id');
    }
}
