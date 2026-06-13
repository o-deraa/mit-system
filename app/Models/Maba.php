<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maba extends Model
{
    protected $table = 'maba';
    protected $primaryKey = 'maba_id';
    protected $guarded = [];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'created_by_maba_id', 'maba_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class, 'maba_id', 'maba_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(MabaKelompokHistory::class, 'maba_id', 'maba_id');
    }
}
