@extends('layouts.public')

@section('title', __('contact.meta_title') . ' - ' . config('app.name', 'NsBase'))
@section('meta_description', __('contact.meta_description'))
@section('og_title', __('contact.meta_title') . ' - ' . config('app.name', 'NsBase'))
@section('og_description', __('contact.meta_description'))

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold uppercase text-primary">{{ __('contact.badge') }}</p>
                    <h1 class="mt-3 text-4xl font-bold">{{ __('contact.title') }}</h1>
                    <p class="mt-4 text-base-content/70">
                        {{ __('contact.subtitle') }}
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-primary/10 p-2 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ __('contact.email_label') }}</h3>
                                <p class="text-sm text-base-content/70">{{ setting('email', 'contact@example.com') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-primary/10 p-2 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ __('contact.phone_label') }}</h3>
                                <p class="text-sm text-base-content/70">{{ setting('phone', '+1 (555) 000-0000') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-primary/10 p-2 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ __('contact.address_label') }}</h3>
                                <p class="text-sm text-base-content/70">{{ setting('address', '123 Main Street, City') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-4">{{ __('contact.form_title') }}</h2>

                        @if (session('success'))
                            <div class="alert alert-success mb-4">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('public.contact.store') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label for="name" class="label">
                                    <span class="label-text">{{ __('contact.form_name') }} <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    class="input input-bordered w-full @error('name') input-error @enderror"
                                    value="{{ old('name') }}"
                                    required
                                />
                                @error('name') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="label">
                                    <span class="label-text">{{ __('contact.form_email') }} <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="input input-bordered w-full @error('email') input-error @enderror"
                                    value="{{ old('email') }}"
                                    required
                                />
                                @error('email') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="phone" class="label">
                                    <span class="label-text">{{ __('contact.form_phone') }}</span>
                                </label>
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    class="input input-bordered w-full @error('phone') input-error @enderror"
                                    value="{{ old('phone') }}"
                                />
                                @error('phone') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="subject" class="label">
                                    <span class="label-text">{{ __('contact.form_subject') }} <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="subject"
                                    name="subject"
                                    type="text"
                                    class="input input-bordered w-full @error('subject') input-error @enderror"
                                    value="{{ old('subject') }}"
                                    required
                                />
                                @error('subject') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="message" class="label">
                                    <span class="label-text">{{ __('contact.form_message') }} <span class="text-error">*</span></span>
                                </label>
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="5"
                                    class="textarea textarea-bordered w-full @error('message') textarea-error @enderror"
                                    required
                                >{{ old('message') }}</textarea>
                                @error('message') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-full">
                                {{ __('contact.form_submit') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
