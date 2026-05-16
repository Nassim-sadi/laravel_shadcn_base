<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('meta_description', 'Structured Laravel business websites with clear Blade frontends and maintainable backend logic.')">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'NsBase'))</title>

        <link rel="canonical" href="{{ url()->current() }}" />

        <meta property="og:title" content="@yield('og_title', config('app.name', 'NsBase'))" />
        <meta property="og:description" content="@yield('og_description', 'Structured Laravel business websites with clear Blade frontends and maintainable backend logic.')" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:type" content="@yield('og_type', 'website')" />
        <meta property="og:site_name" content="{{ config('app.name', 'NsBase') }}" />
        <meta property="og:image" content="@yield('og_image', asset('images/og-default.png'))" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="@yield('og_title', config('app.name', 'NsBase'))" />
        <meta name="twitter:description" content="@yield('og_description', 'Structured Laravel business websites with clear Blade frontends and maintainable backend logic.')" />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-base-100 text-base-content antialiased">
        <header class="border-b border-base-300/70 bg-base-100">
            <div class="navbar mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="navbar-start">
                    <a href="{{ route('home') }}" class="btn btn-ghost px-2 text-xl font-bold">
                        {{ config('app.name', 'NsBase') }}
                    </a>
                </div>

                <nav class="navbar-center hidden lg:flex">
                    <ul class="menu menu-horizontal gap-1 px-1">
<li><a href="{{ route('public.services.index') }}">Services</a></li>
<li><a href="{{ route('public.projects.index') }}">Projects</a></li>
                        <li><a href="{{ route('public.about') }}">About</a></li>
                        <li><a href="{{ route('home') }}#testimonials">Testimonials</a></li>
                        <li><a href="{{ route('home') }}#faq">FAQ</a></li>
                        <li><a href="{{ route('public.contact') }}">Contact</a></li>
                    </ul>
                </nav>

                <div class="navbar-end">
                    <a href="{{ route('public.contact') }}" class="btn btn-primary">Start a project</a>
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="border-t border-base-300 bg-base-200/60">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-base-content/70 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <p>&copy; {{ now()->year }} {{ config('app.name', 'NsBase') }}. All rights reserved.</p>
                <p>Blade frontend. Laravel backend. Vue only where the app needs it.</p>
            </div>
        </footer>
    </body>
</html>
