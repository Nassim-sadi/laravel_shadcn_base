<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Services\Booking\AvailabilityService;

class BookingAvailabilityController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    public function index(BookingService $bookingService)
    {
        $date = request()->query('date');

        if (!$date) {
            return response()->json(['message' => 'date parameter is required'], 400);
        }

        $slots = $this->availabilityService->getAvailableSlots($bookingService->id, $date);

        return response()->json([
            'date' => $date,
            'service_id' => $bookingService->id,
            'slots' => $slots,
        ]);
    }
}
