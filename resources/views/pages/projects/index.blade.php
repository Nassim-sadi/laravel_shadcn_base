@extends('layouts.public')

@section('title', 'Projects - ' . config('app.name', 'NsBase'))
@section('meta_description', 'Browse our portfolio of web development and digital projects.')

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase text-primary">Projects</p>
                    <h1 class="mt-3 text-4xl font-bold">Selected work</h1>
                    <p class="mt-4 text-base-content/70">
                        Published projects come straight from the database, including clients, images, links, and technologies.
                    </p>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                @forelse ($projects as $project)
                    @php
                        $image = $project->image;
                        $imageUrl = $image
                            ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                                ? $image
                                : \Illuminate\Support\Facades\Storage::url($image))
                            : null;
                    @endphp

                    <article class="card border border-base-300 bg-base-100 shadow-sm">
                        @if ($imageUrl)
                            <figure class="aspect-[16/9] bg-base-200">
                                <img src="{{ $imageUrl }}" alt="{{ $project->translated('title') }}" class="h-full w-full object-cover">
                            </figure>
                        @endif

                        <div class="card-body">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3 class="card-title">{{ $project->translated('title') }}</h3>
                                    @if ($project->translated('client'))
                                        <p class="mt-1 text-sm text-base-content/60">{{ $project->translated('client') }}</p>
                                    @endif
                                </div>

                                @if ($project->url)
                                    <a href="{{ $project->url }}" class="btn btn-sm btn-primary" target="_blank" rel="noreferrer">Visit</a>
                                @endif
                            </div>

                            <p class="text-base-content/70">{{ Str::limit($project->translated('description'), 200) }}</p>

                            @if (filled($project->technologies))
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($project->technologies as $technology)
                                        <span class="badge badge-outline">{{ $technology }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="card-actions mt-2">
                                <a href="{{ route('public.projects.show', $project) }}" class="btn btn-sm btn-outline">View details</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="alert lg:col-span-2">
                        <span>No active projects yet. Add projects from the admin panel to publish them here.</span>
                    </div>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div class="mt-10">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
