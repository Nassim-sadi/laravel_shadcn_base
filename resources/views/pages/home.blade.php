@extends('layouts.public')

@section('title', config('app.name', 'NsBase') . ' - Business Websites and Digital Systems')
@section('meta_description', 'Explore services, projects, testimonials, and FAQs powered by Laravel structured content.')

@section('content')
    <section class="bg-base-200/60">
        <div class="mx-auto grid min-h-[calc(100vh-4rem)] w-full max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <div class="max-w-3xl">
                <div class="badge badge-primary badge-outline mb-5">Blade frontend + dynamic content</div>
                <h1 class="text-4xl font-bold leading-tight text-base-content sm:text-5xl lg:text-6xl">
                    Build a clear public website around content you manage from Laravel.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-base-content/70">
                    Services, projects, testimonials, and FAQs are rendered by Blade from structured database records, while Vue stays focused on the admin workspace.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="#contact" class="btn btn-primary btn-lg">Start a project</a>
                    <a href="#projects" class="btn btn-outline btn-lg">View work</a>
                </div>
            </div>

            <div class="rounded-box border border-base-300 bg-base-100 p-6 shadow-xl">
                <div class="stats stats-vertical w-full bg-base-100">
                    <div class="stat">
                        <div class="stat-title">Services</div>
                        <div class="stat-value text-primary">{{ $services->count() }}</div>
                        <div class="stat-desc">Active offers</div>
                    </div>

                    <div class="stat">
                        <div class="stat-title">Projects</div>
                        <div class="stat-value">{{ $projects->count() }}</div>
                        <div class="stat-desc">Published work</div>
                    </div>

                    <div class="stat">
                        <div class="stat-title">Testimonials</div>
                        <div class="stat-value">{{ $testimonials->count() }}</div>
                        <div class="stat-desc">Client proof</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="bg-base-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase text-primary">Services</p>
                <h2 class="mt-3 text-3xl font-bold">What we can build</h2>
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
                            <p class="text-base-content/70">{{ $service->translated('description') }}</p>

                            @if ($service->url)
                                <div class="card-actions mt-2">
                                    <a href="{{ $service->url }}" class="btn btn-sm btn-outline">Learn more</a>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="alert md:col-span-2 lg:col-span-3">
                        <span>No active services yet. Add services from the admin panel to publish them here.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="projects" class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase text-primary">Projects</p>
                    <h2 class="mt-3 text-3xl font-bold">Selected work</h2>
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

                            <p class="text-base-content/70">{{ $project->translated('description') }}</p>

                            @if (filled($project->technologies))
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($project->technologies as $technology)
                                        <span class="badge badge-outline">{{ $technology }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="alert lg:col-span-2">
                        <span>No active projects yet. Add projects from the admin panel to publish them here.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="testimonials" class="bg-base-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase text-primary">Testimonials</p>
                <h2 class="mt-3 text-3xl font-bold">What clients say</h2>
                <p class="mt-4 text-base-content/70">
                    Testimonials can be ordered, rated, translated, and toggled from the admin panel.
                </p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($testimonials as $testimonial)
                    @php
                        $image = $testimonial->image;
                        $imageUrl = $image
                            ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                                ? $image
                                : \Illuminate\Support\Facades\Storage::url($image))
                            : null;
                    @endphp

                    <article class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <div class="rating rating-sm">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" class="mask mask-star-2 bg-primary" @checked($i === (int) $testimonial->rating) disabled>
                                @endfor
                            </div>

                            <p class="mt-4 text-base-content/80">"{{ $testimonial->translated('content') }}"</p>

                            <div class="mt-4 flex items-center gap-3">
                                <div class="avatar placeholder">
                                    <div class="w-12 rounded-full bg-base-300 text-base-content">
                                        @if ($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $testimonial->translated('name') }}">
                                        @else
                                            <span>{{ \Illuminate\Support\Str::of($testimonial->translated('name') ?? '?')->substr(0, 1)->upper() }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold">{{ $testimonial->translated('name') }}</h3>
                                    <p class="text-sm text-base-content/60">
                                        {{ collect([$testimonial->translated('position'), $testimonial->translated('company')])->filter()->join(' · ') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="alert md:col-span-2 lg:col-span-3">
                        <span>No active testimonials yet. Add testimonials from the admin panel to publish them here.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="faq" class="bg-base-200/60 py-16">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase text-primary">FAQ</p>
                <h2 class="mt-3 text-3xl font-bold">Common questions</h2>
                <p class="mt-4 text-base-content/70">
                    Public questions are pulled from active FAQ records and grouped visually by their category label.
                </p>
            </div>

            <div class="space-y-3">
                @forelse ($faqs as $faq)
                    <div class="collapse collapse-arrow border border-base-300 bg-base-100">
                        <input type="radio" name="home-faq" @checked($loop->first)>
                        <div class="collapse-title text-lg font-semibold">
                            {{ $faq->translated('question') }}
                        </div>
                        <div class="collapse-content">
                            @if ($faq->translated('category'))
                                <div class="badge badge-outline mb-3">{{ $faq->translated('category') }}</div>
                            @endif
                            <p class="text-base-content/70">{{ $faq->translated('answer') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="alert">
                        <span>No active FAQs yet. Add FAQ entries from the admin panel to publish them here.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="contact" class="bg-base-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-box border border-base-300 bg-base-100 p-8 shadow-sm">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">Ready to publish dynamic content.</h2>
                        <p class="mt-3 text-base-content/70">Manage the records in admin, then Blade renders the public page from Laravel.</p>
                    </div>
                    <a href="mailto:hello@example.com" class="btn btn-primary btn-lg">Contact us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
