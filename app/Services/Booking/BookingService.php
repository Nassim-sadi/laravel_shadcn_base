<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingConfirmation;
use App\Models\BookingService as BookingServiceModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function create(array $data): Booking
    {
        $service = BookingServiceModel::findOrFail($data['booking_service_id']);

        $slots = $this->availabilityService->getAvailableSlots($service->id, $data['date']);
        $matchingSlot = collect($slots)->first(function ($slot) use ($data) {
            return $slot['start_time'] === $data['start_time'];
        });

        if (!$matchingSlot) {
            throw new \Exception('The selected time slot is not available.');
        }

        $allowDuplicates = setting('booking.allow_duplicate_phone', false);
        if (!$allowDuplicates) {
            $existing = Booking::where('customer_phone', $data['customer_phone'])
                ->whereDate('date', $data['date'])
                ->whereNotIn('status', ['cancelled'])
                ->first();

            if ($existing) {
                throw new \Exception('A booking already exists for this phone number on this date.');
            }
        }

        $endTime = Carbon::parse($data['date'] . ' ' . $data['start_time'])
            ->addMinutes($service->duration_minutes)
            ->format('H:i');

        return DB::transaction(function () use ($data, $service, $endTime) {
            return Booking::create([
                'booking_service_id' => $data['booking_service_id'],
                'date' => $data['date'],
                'start_time' => $data['start_time'],
                'end_time' => $endTime,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    public function confirm(Booking $booking, $user = null): Booking
    {
        $booking->update(['status' => 'confirmed']);

        BookingConfirmation::create([
            'booking_id' => $booking->id,
            'user_id' => $user?->id,
            'action' => 'confirmed',
        ]);

        activity_log('booking.confirmed', [
            'booking_id' => $booking->id,
            'user_id' => $user?->id,
        ]);

        return $booking;
    }

    public function cancel(Booking $booking, $user = null): Booking
    {
        $booking->update(['status' => 'cancelled']);

        BookingConfirmation::create([
            'booking_id' => $booking->id,
            'user_id' => $user?->id,
            'action' => 'cancelled',
        ]);

        activity_log('booking.cancelled', [
            'booking_id' => $booking->id,
            'user_id' => $user?->id,
        ]);

        return $booking;
    }

    public function complete(Booking $booking): Booking
    {
        $booking->update(['status' => 'completed']);

        activity_log('booking.completed', [
            'booking_id' => $booking->id,
        ]);

        return $booking;
    }
}
