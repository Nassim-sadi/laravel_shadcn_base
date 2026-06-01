<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilityRule extends Model
{
    protected $fillable = [
        'booking_service_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getStartTimeAttribute($value)
    {
        return $value ? substr($value, 0, 5) : null;
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? substr($value, 0, 5) : null;
    }

    public function service()
    {
        return $this->belongsTo(BookingService::class, 'booking_service_id');
    }
}
