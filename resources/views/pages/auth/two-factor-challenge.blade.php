@extends('layouts.public')

@section('title', 'Two-Factor Challenge - ' . config('app.name'))
@section('meta_description', 'Enter your authentication code.')

@section('content')
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-sm items-center px-4 py-16">
        <div class="w-full">
            <h1 class="mb-6 text-center text-3xl font-bold">Two-Factor Authentication</h1>

            @if ($errors->any())
                <div class="alert alert-error mb-4">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <p class="text-base-content/70 text-sm">Please enter your authentication code to complete sign in.</p>

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf

                        <div class="form-control">
                            <label class="label" for="code">
                                <span class="label-text">Authentication Code</span>
                            </label>
                            <input id="code" type="text" name="code" class="input input-bordered" inputmode="numeric" autofocus autocomplete="one-time-code">
                        </div>

                        <div class="card-actions mt-4">
                            <button type="submit" class="btn btn-primary w-full">Verify</button>
                        </div>
                    </form>

                    <div class="divider">OR</div>

                    <p class="text-base-content/70 text-sm">Enter one of your recovery codes.</p>

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf

                        <div class="form-control">
                            <label class="label" for="recovery_code">
                                <span class="label-text">Recovery Code</span>
                            </label>
                            <input id="recovery_code" type="text" name="recovery_code" class="input input-bordered" autocomplete="one-time-code">
                        </div>

                        <div class="card-actions mt-4">
                            <button type="submit" class="btn btn-outline w-full">Verify with Recovery Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
