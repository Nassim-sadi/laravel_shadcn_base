<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingSettingsController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Setting::class);

        $settings = Setting::where('group', 'booking')->get();

        return response()->json([
            'data' => $settings->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getValue()];
            }),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('update', Setting::class);

        $validator = Validator::make($request->all(), [
            'time_slot_style' => 'required|in:wheel,list',
            'allow_duplicate_phone' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updated = [];

        $mapping = [
            'time_slot_style' => 'booking.time_slot_style',
            'allow_duplicate_phone' => 'booking.allow_duplicate_phone',
        ];

        $names = [
            'booking.time_slot_style' => 'Time slot style',
            'booking.allow_duplicate_phone' => 'Allow duplicate phone',
        ];

        foreach ($mapping as $inputKey => $settingKey) {
            if ($request->has($inputKey)) {
                Setting::set($settingKey, $request->input($inputKey), [
                    'group' => 'booking',
                    'name' => $names[$settingKey] ?? $inputKey,
                ]);
                $updated[$settingKey] = $request->input($inputKey);
            }
        }

        activity_log('booking.settings.updated', [
            'settings' => $updated,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Booking settings updated successfully.',
            'data' => $updated,
        ]);
    }
}
