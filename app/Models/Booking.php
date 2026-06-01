<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_service_id',
        'date',
        'start_time',
        'end_time',
        'customer_name',
        'customer_phone',
        'notes',
        'status',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 5) : null,
        );
    }

    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? substr($value, 0, 5) : null,
        );
    }

    public function service()
    {
        return $this->belongsTo(BookingService::class, 'booking_service_id');
    }

    public function confirmations()
    {
        return $this->hasMany(BookingConfirmation::class);
    }

    public function reschedules()
    {
        return $this->hasMany(BookingReschedule::class);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecentFirst($query)
    {
        return $query->latest();
    }
}
