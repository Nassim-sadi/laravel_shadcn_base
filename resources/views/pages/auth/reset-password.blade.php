@extends('layouts.public')

@section('title', 'Reset Password - ' . config('app.name'))
@section('meta_description', 'Set a new password.')

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-sm items-center px-4 py-16">
        <div class="w-full">
            <h1 class="mb-6 text-center text-3xl font-bold">Reset Password</h1>

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="card border border-base-300 bg-base-100 shadow-sm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="card-body gap-4">
                    <div class="form-control">
                        <label class="label" for="email">
                            <span class="label-text">Email</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email ?? '') }}" class="input input-bordered @error('email') input-error @enderror" required autofocus autocomplete="email">
                        @error('email')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label" for="password">
                            <span class="label-text">New Password</span>
                        </label>
                        <input id="password" type="password" name="password" class="input input-bordered @error('password') input-error @enderror" required autocomplete="new-password">
                        @error('password')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label" for="password_confirmation">
                            <span class="label-text">Confirm Password</span>
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="input input-bordered @error('password_confirmation') input-error @enderror" required autocomplete="new-password">
                        @error('password_confirmation')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="card-actions">
                        <button type="submit" class="btn btn-primary w-full">Reset Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
