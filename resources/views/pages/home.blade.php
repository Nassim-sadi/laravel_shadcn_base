@extends('layouts.public')

@section('title', config('app.name', 'NsBase') . ' - Laravel Business Kit')
@section('meta_description', 'A Blade-first Laravel business kit for fast public pages, structured content, and maintainable admin workflows.')

@section('content')
    <section class="bg-base-200/60">
        <div class="mx-auto grid min-h-[calc(100vh-4rem)] w-full max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <div class="max-w-3xl">
                <div class="badge badge-primary badge-outline mb-5">Blade frontend + DaisyUI</div>
                <h1 class="text-4xl font-bold leading-tight text-base-content sm:text-5xl lg:text-6xl">
                    Build business websites that stay clear, fast, and easy to maintain.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-base-content/70">
                    NsBase keeps public pages in Blade, uses Laravel for the backend, and saves Vue for admin interfaces that really need application-level interaction.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="#contact" class="btn btn-primary btn-lg">Plan the build</a>
                    <a href="#services" class="btn btn-outline btn-lg">View structure</a>
                </div>
            </div>

            <div class="rounded-box border border-base-300 bg-base-100 p-6 shadow-xl">
                <div class="stats stats-vertical w-full bg-base-100 lg:stats-horizontal">
                    <div class="stat">
                        <div class="stat-title">Public pages</div>
                        <div class="stat-value text-primary">Blade</div>
                        <div class="stat-desc">SEO-friendly and light</div>
                    </div>

                    <div class="stat">
                        <div class="stat-title">Admin UI</div>
                        <div class="stat-value">Vue</div>
                        <div class="stat-desc">Only where it helps</div>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="alert">
                        <span>Laravel handles validation, auth, jobs, permissions, and APIs.</span>
                    </div>
                    <div class="alert alert-info">
                        <span>DaisyUI gives Blade pages fast, consistent UI components.</span>
                    </div>
                    <div class="alert alert-success">
                        <span>The frontend stays boring in the best possible way.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="bg-base-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase text-primary">Structure</p>
                <h2 class="mt-3 text-3xl font-bold">A Blade-first public website</h2>
                <p class="mt-4 text-base-content/70">
                    Start with clear public pages, then add structured modules only when the project needs them.
                </p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <article class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Public Pages</h3>
                        <p>Home, services, projects, FAQ, and contact pages built with Blade layouts and partials.</p>
                    </div>
                </article>

                <article class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Structured Content</h3>
                        <p>Services, projects, testimonials, and SEO fields managed as clear Laravel data.</p>
                    </div>
                </article>

                <article class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Admin When Needed</h3>
                        <p>Vue remains available for tables, modals, filters, and app-style dashboard workflows.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="projects" class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-box bg-base-100 p-8 shadow-sm">
                <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase text-primary">Build path</p>
                        <h2 class="mt-3 text-3xl font-bold">Keep the kit focused.</h2>
                    </div>
                    <div class="steps steps-vertical lg:steps-horizontal">
                        <div class="step step-primary">Blade pages</div>
                        <div class="step step-primary">Laravel modules</div>
                        <div class="step">Vue admin</div>
                        <div class="step">Optional apps</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="bg-base-100 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-box border border-base-300 bg-base-100 p-8 shadow-sm">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-3xl font-bold">Ready for the Blade version.</h2>
                        <p class="mt-3 text-base-content/70">The root page now renders through Blade and DaisyUI.</p>
                    </div>
                    <a href="mailto:hello@example.com" class="btn btn-primary btn-lg">Contact us</a>
                </div>
            </div>
        </div>
    </section>
@endsection
