<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingConfirmation;
use App\Models\BookingReschedule;
use App\Models\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RescheduleService
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function reschedule(Booking $booking, string $newDate, string $newStartTime, string $reason = '', $user = null): Booking
    {
        $service = BookingService::findOrFail($booking->booking_service_id);

        $slots = $this->availabilityService->getAvailableSlots($service->id, $newDate);
        $matchingSlot = collect($slots)->first(function ($slot) use ($newStartTime) {
            return $slot['start_time'] === $newStartTime;
        });

        if (!$matchingSlot) {
            throw new \Exception('The new time slot is not available.');
        }

        $newEndTime = Carbon::parse($newDate . ' ' . $newStartTime)
            ->addMinutes($service->duration_minutes)
            ->format('H:i');

        return DB::transaction(function () use ($booking, $newDate, $newStartTime, $newEndTime, $reason, $user) {
            BookingReschedule::create([
                'booking_id' => $booking->id,
                'old_date' => $booking->date,
                'old_start_time' => $booking->start_time,
                'new_date' => $newDate,
                'new_start_time' => $newStartTime,
                'reason' => $reason,
            ]);

            $booking->update([
                'date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime,
                'status' => 'rescheduled',
            ]);

            BookingConfirmation::create([
                'booking_id' => $booking->id,
                'user_id' => $user?->id,
                'action' => 'rescheduled',
                'notes' => $reason,
            ]);

            activity_log('booking.rescheduled', [
                'booking_id' => $booking->id,
                'user_id' => $user?->id,
            ]);

            return $booking;
        });
    }
}
