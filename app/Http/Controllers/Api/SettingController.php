<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Http\Resources\SettingCollection;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'group' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'value' => 'nullable',
            'default_value' => 'nullable',
            'type' => 'required|string|in:string,integer,boolean,json,array',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);

        $setting = Setting::create($validated);

        return new SettingResource($setting);
    }

    public function show(Setting $setting)
    {
        return new SettingResource($setting);
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'key' => 'sometimes|string|max:255|unique:settings,key,' . $setting->id,
            'group' => 'sometimes|string|max:50',
            'name' => 'sometimes|string|max:255',
            'value' => 'nullable',
            'default_value' => 'nullable',
            'type' => 'sometimes|string|in:string,integer,boolean,json,array',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);

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