<?php

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            $user = $request->user()->load('roles', 'permissions');

            return response()->json(new UserResource($user));
        }

        $home = config('fortify.home', '/admin');

        if ($request->user()->isAdmin()) {
            $home = '/admin';
        }

        return redirect()->intended($home);
    }
}
