<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeBlock extends Model
{
    protected $fillable = [
        'booking_service_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function service()
    {
        return $this->belongsTo(BookingService::class, 'booking_service_id');
    }
}
