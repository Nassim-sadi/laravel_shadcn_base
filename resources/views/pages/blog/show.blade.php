@extends('layouts.public')

@section('title', $blogPost->translated('title') . ' - ' . config('app.name', 'NsBase'))
@section('meta_description', $blogPost->translated('excerpt') ?? Str::limit($blogPost->translated('title'), 160))
@section('og_title', $blogPost->translated('title') . ' - ' . config('app.name', 'NsBase'))
@section('og_description', $blogPost->translated('excerpt') ?? Str::limit($blogPost->translated('title'), 160))

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('public.blog.index') }}" class="link link-hover text-sm">&larr; {{ __('blog.backToBlog') }}</a>
            </div>

            @php
                $image = $blogPost->image;
                $imageUrl = $image
                    ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                        ? $image
                        : \Illuminate\Support\Facades\Storage::url($image))
                    : null;
            @endphp

            @if ($imageUrl)
                <figure class="mb-8 aspect-[16/9] overflow-hidden rounded-box bg-base-200">
                    <img src="{{ $imageUrl }}" alt="{{ $blogPost->translated('title') }}" class="h-full w-full object-cover">
                </figure>
            @endif

            <div class="flex flex-wrap items-center gap-3 text-sm text-base-content/60">
                @if ($blogPost->category)
                    <div class="badge badge-secondary badge-outline">{{ $blogPost->category->translated('name') }}</div>
                @endif
                @if ($blogPost->author)
                    <span>{{ $blogPost->author->name }}</span>
                @endif
                <span>&middot;</span>
                <time datetime="{{ $blogPost->created_at->toIso8601String() }}">{{ $blogPost->created_at->format('M d, Y') }}</time>
            </div>

            <h1 class="mt-4 text-4xl font-bold">{{ $blogPost->translated('title') }}</h1>

            @if ($blogPost->translated('excerpt'))
                <p class="mt-4 text-lg text-base-content/70">{{ $blogPost->translated('excerpt') }}</p>
            @endif

            @if ($blogPost->tags->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($blogPost->tags as $tag)
                        <span class="badge badge-ghost">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            <div class="prose prose-base mt-8 max-w-none text-base-content/80">
                @if ($blogPost->body)
                    {!! $blogPost->body->translated('body') !!}
                @endif
            </div>
        </div>
    </section>
@endsection
