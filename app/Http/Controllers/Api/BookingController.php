<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RescheduleBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use App\Services\Booking\RescheduleService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingService;
    protected $rescheduleService;

    public function __construct(BookingService $bookingService, RescheduleService $rescheduleService)
    {
        $this->bookingService = $bookingService;
        $this->rescheduleService = $rescheduleService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::query()
            ->with(['service', 'confirmations.user', 'reschedules'])
            ->when($request->date, fn ($q, $date) => $q->whereDate('date', $date))
            ->when($request->from_date, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to_date, fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->booking_service_id, fn ($q, $id) => $q->where('booking_service_id', $id))
            ->when($request->search, function ($q, $search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            })
            ->recentFirst();

        if ($request->boolean('calendar')) {
            $bookings = $query->get();
        } else {
            $bookings = $query->paginate($request->per_page ?? 15);
        }

        return BookingResource::collection($bookings);
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        return new BookingResource($booking->load(['service', 'confirmations.user', 'reschedules']));
    }

    public function confirm(Booking $booking)
    {
        $this->authorize('confirm', $booking);

        $this->bookingService->confirm($booking, auth()->user());

        activity_log('booking.confirmed', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
        ]);

        return new BookingResource($booking->fresh());
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('edit', $booking);

        $this->bookingService->cancel($booking, auth()->user());

        activity_log('booking.cancelled', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
        ]);

        return new BookingResource($booking->fresh());
    }

    public function complete(Booking $booking)
    {
        $this->authorize('edit', $booking);

        $this->bookingService->complete($booking);

        activity_log('booking.completed', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
        ]);

        return new BookingResource($booking->fresh());
    }

    public function reschedule(RescheduleBookingRequest $request, Booking $booking)
    {
        $this->authorize('edit', $booking);

        $validated = $request->validated();

        try {
            $this->rescheduleService->reschedule(
                $booking,
                $validated['date'],
                $validated['start_time'],
                $validated['reason'] ?? '',
                auth()->user()
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        activity_log('booking.rescheduled', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
        ]);

        return new BookingResource($booking->fresh());
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('delete', Booking::class);

        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:bookings,id'],
        ]);

        $count = Booking::whereIn('id', $request->ids)->count();
        Booking::whereIn('id', $request->ids)->delete();

        activity_log('booking.bulk_deleted', [
            'count' => $count,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Deleted successfully.',
            'deleted' => $count,
        ]);
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        $booking->delete();

        activity_log('booking.deleted', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
