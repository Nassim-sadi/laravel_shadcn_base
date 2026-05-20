@extends('layouts.public')

@section('title', $product->name)
@section('meta_description', $product->short_description ?? '')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        {{-- Image Gallery --}}
        <div>
            <div class="aspect-square overflow-hidden rounded-lg border bg-white">
                <img id="mainImage" src="{{ $product->media->first()?->image_url ?? asset('images/placeholder.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            @if($product->media->count() > 1)
            <div class="flex gap-2 mt-4 overflow-x-auto">
                @foreach($product->media as $media)
                <button onclick="document.getElementById('mainImage').src='{{ $media->image_url }}'" class="h-20 w-20 shrink-0 overflow-hidden rounded border hover:border-primary transition-colors">
                    @if($media->type === 'video')
                    <img src="{{ $media->thumbnail_path ?? $media->image_url }}" alt="" class="h-full w-full object-cover">
                    @else
                    <img src="{{ $media->image_url }}" alt="" class="h-full w-full object-cover">
                    @endif
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Product Info --}}
        <div class="space-y-6">
            <div>
                @if($product->brand)
                <a href="{{ route('public.catalog.shop', ['brand' => $product->brand->slug]) }}" class="text-sm text-muted-foreground hover:text-primary">{{ $product->brand->name }}</a>
                @endif
                @if($product->category)
                <a href="{{ route('public.catalog.shop', ['category' => $product->category->slug]) }}" class="text-sm text-muted-foreground hover:text-primary">{{ $product->category->name }}</a>
                @endif
                <h1 class="text-3xl font-bold mt-1">{{ $product->name }}</h1>
            </div>

            @if($product->badges)
            <div class="flex gap-2 flex-wrap">
                @foreach($product->badges as $badge)
                <span class="px-3 py-1 text-sm rounded-full text-white
                    @if($badge === 'new') bg-blue-500
                    @elseif($badge === 'sale') bg-red-500
                    @elseif($badge === 'featured') bg-amber-500
                    @elseif($badge === 'popular') bg-green-500
                    @elseif($badge === 'limited') bg-purple-500
                    @else bg-gray-500 @endif">
                    {{ __("catalog.badge.$badge") }}
                </span>
                @endforeach
            </div>
            @endif

            @if($product->price_display)
            <div class="text-2xl font-bold">{{ $product->price_display }}</div>
            @endif

            @if($product->short_description)
            <p class="text-muted-foreground">{{ $product->short_description }}</p>
            @endif

            @if($product->description)
            <div class="prose max-w-none">{{ $product->description }}</div>
            @endif

            @if($product->tags->count())
            <div>
                <h3 class="font-semibold mb-2">{{ __('catalog.tags') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->tags as $tag)
                    <a href="{{ route('public.catalog.index', ['tag' => $tag->slug]) }}" class="px-3 py-1 text-xs rounded-full border hover:bg-muted">{{ $tag->name }}</a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($product->attributes->count())
            <div>
                <h3 class="font-semibold mb-2">{{ __('catalog.attributes') }}</h3>
                <dl class="space-y-2">
                    @foreach($product->attributes as $attribute)
                    <div class="flex justify-between py-2 border-b">
                        <dt class="text-muted-foreground">{{ $attribute->name }}</dt>
                        <dd class="font-medium">{{ $attribute->pivot->value ?? '—' }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif

            <a href="{{ route('public.catalog.quote') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors">
                {{ __('catalog.requestQuote') }}
            </a>
        </div>
    </div>

    {{-- Related Products --}}
    @if($related->count())
    <div class="mt-16">
        <h2 class="text-2xl font-bold mb-6">{{ __('catalog.relatedProducts') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($related as $relatedProduct)
            <a href="{{ route('public.catalog.show', $relatedProduct->slug) }}" class="group block border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                @if($relatedProduct->media->first()?->image_url)
                <div class="aspect-square overflow-hidden">
                    <img src="{{ $relatedProduct->media->first()->image_url }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold group-hover:text-primary transition-colors">{{ $relatedProduct->name }}</h3>
                    @if($relatedProduct->price_display)
                    <p class="text-lg font-bold mt-1">{{ $relatedProduct->price_display }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
