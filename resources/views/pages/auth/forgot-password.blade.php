@extends('layouts.public')

@section('title', 'Forgot Password - ' . config('app.name'))
@section('meta_description', 'Reset your password.')

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-sm items-center px-4 py-16">
        <div class="w-full">
            <h1 class="mb-2 text-center text-3xl font-bold">Forgot Password</h1>
            <p class="mb-6 text-center text-base-content/70">Enter your email and we'll send you a reset link.</p>

            @if (session('status'))
                <div class="alert alert-success mb-4">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="card border border-base-300 bg-base-100 shadow-sm">
                @csrf

                <div class="card-body gap-4">
                    <div class="form-control">
                        <label class="label" for="email">
                            <span class="label-text">Email</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="input input-bordered @error('email') input-error @enderror" required autofocus autocomplete="email">
                        @error('email')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="card-actions flex-col gap-2">
                        <button type="submit" class="btn btn-primary w-full">Send Reset Link</button>

                        <p class="text-center text-sm">
                            <a href="{{ route('login') }}" class="link link-hover">Back to sign in</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
