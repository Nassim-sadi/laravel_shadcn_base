<?php

namespace App\Http\Controllers\Public\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::password(32)),
                'role' => 'user',
                'is_active' => true,
                'locale' => app()->getLocale(),
            ]);
        }

        if (! $user->is_active) {
            return redirect('/login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/');
    }
}
