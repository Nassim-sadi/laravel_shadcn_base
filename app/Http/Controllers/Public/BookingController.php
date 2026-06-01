<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\BookingService;
use App\Services\Booking\AvailabilityService;
use App\Services\Booking\BookingService as BookingDomainService;

class BookingController extends Controller
{
    protected $bookingService;
    protected $availabilityService;

    public function __construct(BookingDomainService $bookingService, AvailabilityService $availabilityService)
    {
        $this->bookingService = $bookingService;
        $this->availabilityService = $availabilityService;
    }

    public function index()
    {
        $services = BookingService::where('is_active', true)
            ->orderBy('order')
            ->get();

        $timeSlotStyle = setting('booking.time_slot_style', 'wheel');

        return view('pages.booking.index', compact('services', 'timeSlotStyle'));
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $booking = $this->bookingService->create($request->validated());
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => ['start_time' => [$e->getMessage()]],
                ], 422);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        activity_log('booking.created', [
            'booking_id' => $booking->id,
            'customer_phone' => $booking->customer_phone,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('booking.confirmationMessage'),
                'data' => new BookingResource($booking),
            ], 201);
        }

        return redirect()->back()->with('success', __('booking.confirmationMessage'));
    }

    public function availability($serviceId)
    {
        $date = request('date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $slots = $this->availabilityService->getAvailableSlots($serviceId, $date);

        return response()->json(['data' => $slots]);
    }
}
