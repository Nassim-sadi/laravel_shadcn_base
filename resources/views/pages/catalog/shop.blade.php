@extends('layouts.public')

@section('title', $categoryName ? "$categoryName - ".__('catalog.title') : __('catalog.title'))
@section('meta_description', __('catalog.shopDescription'))

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Sidebar Filters --}}
        <aside class="lg:w-64 shrink-0">
            <div class="sticky top-24 space-y-6">
                {{-- Search --}}
                <form method="GET" action="{{ route('public.catalog.shop') }}" class="space-y-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('catalog.search') }}" class="w-full px-3 py-2 border rounded-lg">
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-primary-foreground rounded-lg">{{ __('catalog.search') }}</button>
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                </form>

                {{-- Categories --}}
                @if($categories->count())
                <div>
                    <h3 class="font-semibold mb-2">{{ __('catalog.categories') }}</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('public.catalog.shop') }}" class="block px-3 py-1 rounded @if(!request('category')) bg-primary text-primary-foreground @else hover:bg-muted @endif">
                                {{ __('catalog.all') }}
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li>
                            <a href="{{ route('public.catalog.shop', ['category' => $cat->slug]) }}" class="block px-3 py-1 rounded @if(request('category') === $cat->slug) bg-primary text-primary-foreground @else hover:bg-muted @endif">
                                {{ $cat->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Brands --}}
                @if($brands->count())
                <div>
                    <h3 class="font-semibold mb-2">{{ __('catalog.brands') }}</h3>
                    <ul class="space-y-1">
                        @foreach($brands as $brand)
                        <li>
                            <a href="{{ route('public.catalog.shop', ['brand' => $brand->slug, 'category' => request('category')]) }}" class="block px-3 py-1 rounded @if(request('brand') === $brand->slug) bg-primary text-primary-foreground @else hover:bg-muted @endif">
                                {{ $brand->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Badges --}}
                <div>
                    <h3 class="font-semibold mb-2">{{ __('catalog.badges') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['new', 'sale', 'featured', 'popular', 'limited'] as $badge)
                        <a href="{{ route('public.catalog.shop', ['badge' => $badge, 'category' => request('category')]) }}" class="px-3 py-1 text-xs rounded-full border @if(request('badge') === $badge) bg-primary text-primary-foreground border-primary @else hover:bg-muted @endif">
                            {{ __("catalog.badge.$badge") }}
                        </a>
                        @endforeach
                    </div>
                </div>

                @if(request()->hasAny(['search', 'category', 'brand', 'badge']))
                <a href="{{ route('public.catalog.shop') }}" class="block text-sm text-primary hover:underline">{{ __('catalog.clearFilters') }}</a>
                @endif
            </div>
        </aside>

        {{-- Product Grid --}}
        <main class="flex-1">
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    @if(request('category'))
                        {{ $categoryName }}
                    @endif
                    @if(request('brand'))
                        {{ $brandName }}
                    @endif
                    ({{ $products->total() }} {{ __('catalog.products') }})
                </p>
            </div>

            @if($products->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                <a href="{{ route('public.catalog.show', $product->slug) }}" class="group block border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    @if($product->media->first()?->image_url)
                    <div class="aspect-square overflow-hidden">
                        <img src="{{ $product->media->first()->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    @else
                    <div class="aspect-square bg-muted flex items-center justify-center">
                        <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    @endif
                    <div class="p-4">
                        @if($product->brand)
                        <div class="text-xs text-muted-foreground mb-1">{{ $product->brand->name }}</div>
                        @endif
                        <h3 class="font-semibold group-hover:text-primary transition-colors">{{ $product->name }}</h3>
                        @if($product->price_display)
                        <p class="text-lg font-bold mt-1">{{ $product->price_display }}</p>
                        @endif
                        @if($product->badges)
                        <div class="flex gap-1 mt-2 flex-wrap">
                            @foreach($product->badges as $badge)
                            <span class="px-2 py-0.5 text-xs rounded-full text-white
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
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-12 text-muted-foreground">
                <p>{{ __('catalog.noProducts') }}</p>
            </div>
            @endif
        </main>
    </div>
</div>
@endsection
