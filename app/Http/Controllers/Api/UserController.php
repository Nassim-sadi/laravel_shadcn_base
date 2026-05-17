<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
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
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
            ->when($request->role, fn($q, $role) => $q->where('role', $role))
            ->when(!$request->include_active || $request->include_active === 'true', fn($q) => $q->where('is_active', true))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return new UserCollection($users);
    }

    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'user';

        $user = User::create($validated);

        activity_log('user.created', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        return new UserResource($user);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('roles', 'permissions');
        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        activity_log('user.updated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'id' => ['You cannot delete your own account.'],
            ]);
        }

        $userName = $user->name;
        $user->delete();

        activity_log('user.deleted', [
            'user_name' => $userName,
        ]);

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function invite(Request $request)
    {
        $this->authorize('create', User::class);

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

        activity_log('user.invited', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // TODO: Send invitation email with password

        return response()->json([
            'message' => 'User invited successfully',
        ])->setStatusCode(201);
    }

    public function assignRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user->assignRole($validated['role']);

        activity_log('user.role_assigned', [
            'user_id' => $user->id,
            'role' => $validated['role'],
        ]);

        return new UserResource($user);
    }

    public function givePermission(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $user->givePermissionTo($validated['permission']);

        activity_log('user.permission_granted', [
            'user_id' => $user->id,
            'permission' => $validated['permission'],
        ]);

        return new UserResource($user);
    }

    public function revokePermission(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $user->revokePermissionTo($validated['permission']);

        activity_log('user.permission_revoked', [
            'user_id' => $user->id,
            'permission' => $validated['permission'],
        ]);

        return new UserResource($user);
    }

    public function uploadAvatar(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $validated['avatar'];
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            $user->update(['avatar' => $path]);

            activity_log('user.avatar_uploaded', [
                'user_id' => $user->id,
                'avatar_path' => $path,
            ]);

            return response()->json([
                'message' => 'Avatar uploaded successfully',
                'avatar' => $path,
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    public function deleteAvatar(User $user)
    {
        $this->authorize('update', $user);

        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        activity_log('user.avatar_deleted', [
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Avatar deleted successfully']);
    }
}