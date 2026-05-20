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
        $this->authorize('viewAny', Setting::class);

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
        $this->authorize('create', Setting::class);

        $validated = $request->validated();

        $setting = Setting::create($validated);

        activity_log('setting.created', [
            'setting_id' => $setting->id,
            'setting_key' => $setting->key,
            'user_id' => auth()->id(),
        ]);

        return new SettingResource($setting);
    }

    public function show(Setting $setting)
    {
        $this->authorize('view', $setting);

        return new SettingResource($setting);
    }

    public function update(SettingRequest $request, Setting $setting)
    {
        $this->authorize('update', $setting);

        $validated = $request->validated();

        $setting->update($validated);

        activity_log('setting.updated', [
            'setting_id' => $setting->id,
            'setting_key' => $setting->key,
            'user_id' => auth()->id(),
        ]);

        return new SettingResource($setting);
    }

    public function destroy(Setting $setting)
    {
        $this->authorize('delete', $setting);

        $protectedKeys = [
            'site_name',
            'site_email',
            'site_phone',
            'site_address',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'mail_from_address',
        ];

        if (in_array($setting->key, $protectedKeys, true)) {
            return response()->json([
                'message' => 'Cannot delete system setting: '.$setting->key,
            ], 403);
        }

        $setting->delete();

        activity_log('setting.deleted', [
            'setting_id' => $setting->id,
            'setting_key' => $setting->key,
            'user_id' => auth()->id(),
        ]);

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