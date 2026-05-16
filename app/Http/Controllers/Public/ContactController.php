<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $key = 'contact-form:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput()
                ->withErrors(['email' => "Too many attempts. Please try again in {$seconds} seconds."]);
        }

        RateLimiter::hit($key, 10);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        ContactMessage::create($validated);

        activity_log('contact_message.public_created', [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $notificationData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ];

        Notification::route('mail', $validated['email'])
            ->notify(new ContactMessageNotification('contact_confirmation', $notificationData));

        Notification::route('mail', setting('email', 'contact@example.com'))
            ->notify(new ContactMessageNotification('contact_notification_admin', $notificationData));

        return redirect()->route('public.contact')
            ->with('success', __('contact.success_message'));
    }
}
