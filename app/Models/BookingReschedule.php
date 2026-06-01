<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReschedule extends Model
{
    protected $fillable = [
        'booking_id',
        'old_date',
        'old_start_time',
        'new_date',
        'new_start_time',
        'reason',
    ];

    protected $casts = [
        'old_date' => 'date',
        'new_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
