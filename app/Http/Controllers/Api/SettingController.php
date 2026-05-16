<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SettingRequest;
use App\Http\Resources\SettingResource;
use App\Http\Resources\SettingCollection;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $query = Setting::query()
            ->when($request->group, fn($q, $group) => $q->group($group))
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('key', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            ->when($request->is_public !== null, fn($q) => $q->where('is_public', $request->is_public))
            ->orderBy($request->sort_by ?? 'group', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new SettingCollection($query);
    }

    public function store(SettingRequest $request)
    {
        $validated = $request->validated();

        $setting = Setting::create($validated);

        return new SettingResource($setting);
    }

    public function show(Setting $setting)
    {
        return new SettingResource($setting);
    }

    public function update(SettingRequest $request, Setting $setting)
    {
        $validated = $request->validated();

        $setting->update($validated);

        return new SettingResource($setting);
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return response()->json(['message' => 'Setting deleted successfully']);
    }

    /**
     * Get a setting value by key (public endpoint)
     */
    public function value(string $key)
    {
        $setting = Setting::where('key', $key)
            ->where('is_public', true)
            ->firstOrFail();

        return response()->json([
            'key' => $setting->key,
            'value' => $setting->getValue(),
            'type' => $setting->type,
        ]);
    }
}