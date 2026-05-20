@extends('layouts.public')

@section('title', __('catalog.requestQuote'))
@section('meta_description', __('catalog.quoteDescription'))

@section('content')
<div class="container mx-auto px-4 py-12 max-w-2xl">
    <h1 class="text-3xl font-bold mb-2">{{ __('catalog.requestQuote') }}</h1>
    <p class="text-muted-foreground mb-8">{{ __('catalog.quoteDescription') }}</p>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('public.catalog.quote.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium mb-1">{{ __('catalog.quoteName') }} *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border rounded-lg @error('name') border-red-500 @enderror">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium mb-1">{{ __('catalog.quoteEmail') }} *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded-lg @error('email') border-red-500 @enderror">
            </div>
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium mb-1">{{ __('catalog.quotePhone') }}</label>
            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border rounded-lg @error('phone') border-red-500 @enderror">
        </div>

        @if(isset($product))
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <div class="p-4 bg-muted rounded-lg">
            <span class="text-sm text-muted-foreground">{{ __('catalog.quoteFor') }}</span>
            <p class="font-medium">{{ $product->name }}</p>
        </div>
        @else
        <div>
            <label for="product_id" class="block text-sm font-medium mb-1">{{ __('catalog.quoteProduct') }}</label>
            <select name="product_id" id="product_id" class="w-full px-3 py-2 border rounded-lg @error('product_id') border-red-500 @enderror">
                <option value="">{{ __('catalog.selectProduct') }}</option>
                @foreach(\App\Models\CatalogProduct::where('is_active', true)->where('is_published', true)->get() as $p)
                <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label for="message" class="block text-sm font-medium mb-1">{{ __('catalog.quoteMessage') }}</label>
            <textarea name="message" id="message" rows="4" class="w-full px-3 py-2 border rounded-lg @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
        </div>

        <button type="submit" class="w-full px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors">
            {{ __('catalog.submitQuote') }}
        </button>
    </form>
</div>
@endsection
