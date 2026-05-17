@extends('layouts.public')

@section('title', 'Verify Email - ' . config('app.name'))
@section('meta_description', 'Verify your email address.')

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-lg items-center px-4 py-16">
        <div class="w-full">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body items-center text-center gap-4">
                    <h1 class="text-3xl font-bold">Verify Your Email</h1>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success">A new verification link has been sent to your email address.</div>
                    @endif

                    <p class="text-base-content/70">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking the link we just emailed to you? If you didn't receive the email, we'll gladly send you another.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Log out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
