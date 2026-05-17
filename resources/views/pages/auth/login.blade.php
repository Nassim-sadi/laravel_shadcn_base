@extends('layouts.public')

@section('title', 'Sign In - ' . config('app.name'))
@section('meta_description', 'Sign in to your account.')

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-sm items-center px-4 py-16">
        <div class="w-full">
            <h1 class="mb-6 text-center text-3xl font-bold">Sign In</h1>

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

            <form method="POST" action="{{ route('login') }}" class="card border border-base-300 bg-base-100 shadow-sm">
                @csrf

                <div class="card-body gap-4">
                    <div class="form-control">
                        <label class="label" for="email">
                            <span class="label-text">Email</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="input input-bordered @error('email') input-error @enderror" required autofocus autocomplete="username">
                        @error('email')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label" for="password">
                            <span class="label-text">Password</span>
                        </label>
                        <input id="password" type="password" name="password" class="input input-bordered @error('password') input-error @enderror" required autocomplete="current-password">
                        @error('password')
                            <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="remember" class="checkbox checkbox-sm">
                            <span class="label-text">Remember me</span>
                        </label>
                    </div>

                    <div class="card-actions flex-col gap-2">
                        <button type="submit" class="btn btn-primary w-full">Sign In</button>

                        <div class="flex items-center justify-between text-sm">
                            <a href="{{ route('register') }}" class="link link-hover">Create account</a>
                            <a href="{{ route('password.request') }}" class="link link-hover">Forgot password?</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
