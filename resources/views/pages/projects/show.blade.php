@extends('layouts.public')

@section('title', $project->translated('title') . ' - ' . config('app.name', 'NsBase'))
@section('meta_description', $project->translated('seo_description') ?? Str::limit($project->translated('description'), 160))
@section('og_title', $project->translated('title') . ' - ' . config('app.name', 'NsBase'))
@section('og_description', $project->translated('seo_description') ?? Str::limit($project->translated('description'), 160))

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('public.projects.index') }}" class="link link-hover text-sm">&larr; Back to projects</a>
            </div>

            @php
                $image = $project->image;
                $imageUrl = $image
                    ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                        ? $image
                        : \Illuminate\Support\Facades\Storage::url($image))
                    : null;
            @endphp

            <div class="grid gap-10 lg:grid-cols-[1.2fr_1fr]">
                @if ($imageUrl)
                    <figure class="aspect-[4/3] overflow-hidden rounded-box bg-base-200">
                        <img src="{{ $imageUrl }}" alt="{{ $project->translated('title') }}" class="h-full w-full object-cover">
                    </figure>
                @endif

                <div>
                    <h1 class="text-4xl font-bold">{{ $project->translated('title') }}</h1>

                    @if ($project->translated('client'))
                        <p class="mt-2 text-lg text-base-content/60">Client: {{ $project->translated('client') }}</p>
                    @endif

                    <p class="mt-6 text-lg leading-relaxed text-base-content/70">{{ $project->translated('description') }}</p>

                    @if (filled($project->technologies))
                        <div class="mt-6">
                            <p class="text-sm font-semibold uppercase text-base-content/60 mb-2">Technologies</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($project->technologies as $technology)
                                    <span class="badge badge-outline">{{ $technology }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($project->url)
                        <div class="mt-8">
                            <a href="{{ $project->url }}" class="btn btn-primary" target="_blank" rel="noreferrer">
                                Visit project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
