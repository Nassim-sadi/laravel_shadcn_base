<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingConfirmation extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'action',
        'notes',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
