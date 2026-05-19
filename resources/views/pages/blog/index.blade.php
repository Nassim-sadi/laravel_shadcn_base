@extends('layouts.public')

@section('title', __('public.nav.blog') . ' - ' . config('app.name', 'NsBase'))
@section('meta_description', __('public.nav.blog') . ' - ' . config('app.name', 'NsBase'))
@section('og_title', __('public.nav.blog') . ' - ' . config('app.name', 'NsBase'))
@section('og_description', __('public.nav.blog') . ' - ' . config('app.name', 'NsBase'))

@section('content')
    <section class="bg-base-200/60 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase text-primary">{{ __('public.nav.blog') }}</p>
                <h1 class="mt-3 text-4xl font-bold">{{ __('blog.title') }}</h1>
                <p class="mt-4 text-base-content/70">
                    {{ __('blog.subtitle') }}
                </p>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($posts as $post)
                    @php
                        $image = $post->image;
                        $imageUrl = $image
                            ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://', '/'])
                                ? $image
                                : \Illuminate\Support\Facades\Storage::url($image))
                            : null;
                    @endphp

                    <article class="card border border-base-300 bg-base-100 shadow-sm">
                        @if ($imageUrl)
                            <figure class="aspect-[16/9] bg-base-200">
                                <img src="{{ $imageUrl }}" alt="{{ $post->translated('title') }}" class="h-full w-full object-cover">
                            </figure>
                        @endif

                        <div class="card-body">
                            @if ($post->category)
                                <div class="badge badge-secondary badge-outline mb-2">{{ $post->category->translated('name') }}</div>
                            @endif

                            <h3 class="card-title">{{ $post->translated('title') }}</h3>
                            <p class="text-base-content/70">{{ Str::limit($post->translated('excerpt'), 120) }}</p>

                            <div class="mt-2 flex items-center gap-2 text-sm text-base-content/60">
                                @if ($post->author)
                                    <span>{{ $post->author->name }}</span>
                                @endif
                                <span>&middot;</span>
                                <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->format('M d, Y') }}</time>
                            </div>

                            <div class="card-actions mt-2">
                                <a href="{{ route('public.blog.show', $post) }}" class="btn btn-sm btn-outline">{{ __('public.readMore') }}</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="alert md:col-span-2 lg:col-span-3">
                        <span>{{ __('public.noBlogPosts') }}</span>
                    </div>
                @endforelse
            </div>

            @if ($posts->hasPages())
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
