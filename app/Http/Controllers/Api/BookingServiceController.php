<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingServiceRequest;
use App\Http\Requests\Api\UpdateBookingServiceRequest;
use App\Http\Resources\BookingServiceResource;
use App\Models\AvailabilityRule;
use App\Models\BookingService;
use App\Models\TimeBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingServiceController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', BookingService::class);

        $services = BookingService::query()
            ->with(['availabilityRules', 'timeBlocks'])
            ->withCount('bookings')
            ->ordered()
            ->get();

        return BookingServiceResource::collection($services);
    }

    public function all()
    {
        $services = BookingService::query()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'duration_minutes', 'price']);

        return BookingServiceResource::collection($services);
    }

    public function store(StoreBookingServiceRequest $request)
    {
        $this->authorize('create', BookingService::class);

        $validated = $request->validated();
        $rules = $validated['availability_rules'] ?? [];
        unset($validated['availability_rules']);

        $service = BookingService::create($validated);

        foreach ($rules as $rule) {
            AvailabilityRule::create([
                'booking_service_id' => $service->id,
                'day_of_week' => $rule['day_of_week'],
                'start_time' => $rule['start_time'],
                'end_time' => $rule['end_time'],
                'is_active' => true,
            ]);
        }

        activity_log('booking_service.created', [
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return new BookingServiceResource($service->load(['availabilityRules', 'timeBlocks']));
    }

    public function show(BookingService $bookingService)
    {
        $this->authorize('view', $bookingService);

        return new BookingServiceResource($bookingService->load(['availabilityRules', 'timeBlocks']));
    }

    public function update(UpdateBookingServiceRequest $request, BookingService $bookingService)
    {
        $this->authorize('edit', $bookingService);

        $validated = $request->validated();
        $rules = $validated['availability_rules'] ?? null;
        unset($validated['availability_rules']);

        $bookingService->update($validated);

        if ($rules !== null) {
            $bookingService->availabilityRules()->delete();
            foreach ($rules as $rule) {
                AvailabilityRule::create([
                    'booking_service_id' => $bookingService->id,
                    'day_of_week' => $rule['day_of_week'],
                    'start_time' => $rule['start_time'],
                    'end_time' => $rule['end_time'],
                    'is_active' => true,
                ]);
            }
        }

        activity_log('booking_service.updated', [
            'service_id' => $bookingService->id,
            'user_id' => auth()->id(),
        ]);

        return new BookingServiceResource($bookingService->load(['availabilityRules', 'timeBlocks']));
    }

    public function destroy(BookingService $bookingService)
    {
        $this->authorize('delete', $bookingService);

        $bookingCount = $bookingService->bookings()->whereNotIn('status', ['cancelled'])->count();
        if ($bookingCount > 0) {
            return response()->json([
                'message' => "Cannot delete service. It has {$bookingCount} active booking(s).",
            ], 409);
        }

        $bookingService->delete();

        activity_log('booking_service.deleted', [
            'service_id' => $bookingService->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Booking service deleted successfully']);
    }

    public function toggleStatus(BookingService $bookingService)
    {
        $this->authorize('edit', $bookingService);

        $bookingService->update(['is_active' => !$bookingService->is_active]);

        activity_log('booking_service.toggled', [
            'service_id' => $bookingService->id,
            'is_active' => $bookingService->is_active,
            'user_id' => auth()->id(),
        ]);

        return new BookingServiceResource($bookingService);
    }

    public function storeTimeBlock(Request $request, BookingService $bookingService)
    {
        $this->authorize('edit', $bookingService);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'type' => ['required', 'in:closure,holiday,leave'],
            'reason' => ['nullable', 'string'],
        ]);

        $block = $bookingService->timeBlocks()->create($validated);

        return response()->json(['message' => 'Time block created', 'data' => $block]);
    }

    public function destroyTimeBlock(BookingService $bookingService, TimeBlock $timeBlock)
    {
        $this->authorize('edit', $bookingService);

        if ($timeBlock->booking_service_id !== $bookingService->id) {
            abort(404);
        }

        $timeBlock->delete();

        return response()->json(['message' => 'Time block deleted']);
    }
}
