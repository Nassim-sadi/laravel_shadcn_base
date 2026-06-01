<?php

namespace App\Services\Booking;

use App\Models\AvailabilityRule;
use App\Models\Booking;
use App\Models\TimeBlock;
use Carbon\Carbon;

class AvailabilityService
{
    public function getAvailableSlots(int $serviceId, string $date): array
    {
        $service = \App\Models\BookingService::find($serviceId);
        if (!$service) {
            return [];
        }

        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->dayOfWeek;

        $rule = AvailabilityRule::where('booking_service_id', $serviceId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$rule) {
            return [];
        }

        $blocks = TimeBlock::where('booking_service_id', $serviceId)
            ->whereDate('date', $date)
            ->get();

        $bookings = Booking::where('booking_service_id', $serviceId)
            ->whereDate('date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $start = Carbon::parse($date . ' ' . $rule->start_time);
        $end = Carbon::parse($date . ' ' . $rule->end_time);
        $duration = $service->duration_minutes;

        $slots = [];
        $current = $start->copy();

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $current->copy()->addMinutes($duration);

            $isBlocked = $blocks->first(function ($block) use ($current, $slotEnd) {
                if (!$block->start_time || !$block->end_time) {
                    return true;
                }
                $blockStart = Carbon::parse($block->date->format('Y-m-d') . ' ' . $block->start_time);
                $blockEnd = Carbon::parse($block->date->format('Y-m-d') . ' ' . $block->end_time);
                return !($slotEnd->lte($blockStart) || $current->gte($blockEnd));
            });

            if (!$isBlocked) {
                $isBooked = $bookings->first(function ($booking) use ($current, $slotEnd) {
                    $bookingStart = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $booking->start_time);
                    $bookingEnd = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $booking->end_time);
                    return !($slotEnd->lte($bookingStart) || $current->gte($bookingEnd));
                });

                if (!$isBooked) {
                    $slots[] = [
                        'start_time' => $current->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                    ];
                }
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }
}
