@extends('layouts.public')

@section('title', 'Services - ' . config('app.name', 'NsBase'))
@section('meta_description', 'Explore our range of web development and digital services.')
@section('og_title', 'Services - ' . config('app.name', 'NsBase'))
@section('og_description', 'Explore our range of web development and digital services.')

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase text-primary">Services</p>
                <h1 class="mt-3 text-4xl font-bold">What we can build</h1>
                <p class="mt-4 text-base-content/70">
                    Active services are managed in the admin panel and displayed here with locale fallback.
                </p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($services as $service)
                    @php
                        $image = $service->image;
                        $imageUrl = $image
                            ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                                ? $image
                                : \Illuminate\Support\Facades\Storage::url($image))
                            : null;
                    @endphp

                    <article class="card border border-base-300 bg-base-100 shadow-sm">
                        @if ($imageUrl)
                            <figure class="aspect-[16/9] bg-base-200">
                                <img src="{{ $imageUrl }}" alt="{{ $service->translated('title') }}" class="h-full w-full object-cover">
                            </figure>
                        @endif

                        <div class="card-body">
                            @if ($service->icon)
                                <div class="badge badge-secondary badge-outline mb-2">{{ $service->icon }}</div>
                            @endif

                            <h3 class="card-title">{{ $service->translated('title') }}</h3>
                            <p class="text-base-content/70">{{ Str::limit($service->translated('description'), 150) }}</p>

                            <div class="card-actions mt-2">
                                <a href="{{ route('public.services.show', $service) }}" class="btn btn-sm btn-outline">Learn more</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="alert md:col-span-2 lg:col-span-3">
                        <span>No active services yet. Add services from the admin panel to publish them here.</span>
                    </div>
                @endforelse
            </div>

            @if ($services->hasPages())
                <div class="mt-10">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
