<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when(!$request->include_active || $request->include_active === 'true', fn($q) => $q->where('is_active', true))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return new UserCollection($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'sometimes|string|in:admin,user,guest',
            'locale' => 'sometimes|string|in:en,fr,ar',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'user';

        $user = User::create($validated);

        return new UserResource($user);
    }

    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|string|in:super_admin,admin,user,guest',
            'locale' => 'sometimes|string|in:en,fr,ar',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'id' => ['You cannot delete your own account.'],
            ]);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function invite(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'role' => 'sometimes|string|in:admin,user,guest',
        ]);

        $password = Str::random(16);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => $validated['role'] ?? 'user',
            'is_active' => true,
        ]);

        // TODO: Send invitation email with password

        return response()->json([
            'message' => 'User invited successfully',
            'temporary_password' => $password,
        ])->setStatusCode(201);
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->assignRole($validated['role']);

        return new UserResource($user);
    }

    public function givePermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $user->givePermissionTo($validated['permission']);

        return new UserResource($user);
    }

    public function revokePermission(Request $request, User $user)
    {
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $user->revokePermissionTo($validated['permission']);

        return new UserResource($user);
    }

    public function uploadAvatar(Request $request, User $user)
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $validated['avatar'];
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            $user->update(['avatar' => $path]);

            return response()->json([
                'message' => 'Avatar uploaded successfully',
                'avatar' => $path,
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function deleteAvatar(User $user)
    {
        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json(['message' => 'Avatar deleted successfully']);
    }
}