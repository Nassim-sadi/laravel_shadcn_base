@extends('layouts.public')

@section('title', ($about['about.meta_title'] ?? 'About') . ' - ' . config('app.name', 'NsBase'))
@section('meta_description', $about['about.meta_description'] ?? '')
@section('og_title', ($about['about.meta_title'] ?? 'About') . ' - ' . config('app.name', 'NsBase'))
@section('og_description', $about['about.meta_description'] ?? '')

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <p class="text-sm font-semibold uppercase text-primary text-center">{{ $about['about.badge'] ?? '' }}</p>
                <h1 class="mt-3 text-4xl font-bold text-center">{{ $about['about.title'] ?? '' }}</h1>
                <p class="mt-4 text-base-content/70 text-center text-lg">
                    {{ $about['about.subtitle'] ?? '' }}
                </p>
            </div>

            <div class="mt-12 prose prose-lg max-w-3xl mx-auto">
                <h2>{{ $about['about.story_title'] ?? '' }}</h2>
                <p>{{ $about['about.story_body'] ?? '' }}</p>

                <h2>{{ $about['about.mission_title'] ?? '' }}</h2>
                <p>{{ $about['about.mission_body'] ?? '' }}</p>

                <h2>{{ $about['about.values_title'] ?? '' }}</h2>
                <ul>
                    <li><strong>{{ $about['about.value_1_title'] ?? '' }}:</strong> {{ $about['about.value_1_body'] ?? '' }}</li>
                    <li><strong>{{ $about['about.value_2_title'] ?? '' }}:</strong> {{ $about['about.value_2_body'] ?? '' }}</li>
                    <li><strong>{{ $about['about.value_3_title'] ?? '' }}:</strong> {{ $about['about.value_3_body'] ?? '' }}</li>
                    <li><strong>{{ $about['about.value_4_title'] ?? '' }}:</strong> {{ $about['about.value_4_body'] ?? '' }}</li>
                </ul>
            </div>
        </div>
    </section>
@endsection
