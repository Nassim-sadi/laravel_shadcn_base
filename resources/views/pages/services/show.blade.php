@extends('layouts.public')

@section('title', $service->translated('title') . ' - ' . config('app.name', 'NsBase'))
@section('meta_description', $service->translated('seo_description') ?? Str::limit($service->translated('description'), 160))
@section('og_title', $service->translated('title') . ' - ' . config('app.name', 'NsBase'))
@section('og_description', $service->translated('seo_description') ?? Str::limit($service->translated('description'), 160))

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('public.services.index') }}" class="link link-hover text-sm">&larr; {{ __('public.backToServices') }}</a>
            </div>

            @php
                $image = $service->image;
                $imageUrl = $image
                    ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                        ? $image
                        : \Illuminate\Support\Facades\Storage::url($image))
                    : null;
            @endphp

            <div class="grid gap-10 lg:grid-cols-[1fr_1.2fr]">
                @if ($imageUrl)
                    <figure class="aspect-[4/3] overflow-hidden rounded-box bg-base-200">
                        <img src="{{ $imageUrl }}" alt="{{ $service->translated('title') }}" class="h-full w-full object-cover">
                    </figure>
                @endif

                <div>
                    @if ($service->icon)
                        <div class="badge badge-secondary badge-outline mb-4 text-base">{{ $service->icon }}</div>
                    @endif

                    <h1 class="text-4xl font-bold">{{ $service->translated('title') }}</h1>
                    <p class="mt-6 text-lg leading-relaxed text-base-content/70">{{ $service->translated('description') }}</p>

                    @if ($service->url)
                        <div class="mt-8">
                            <a href="{{ $service->url }}" class="btn btn-primary" target="_blank" rel="noreferrer">
                                {{ __('services.visitSite') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
