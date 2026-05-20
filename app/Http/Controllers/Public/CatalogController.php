<?php

namespace App\Http\Controllers\Public;

use App\Models\CatalogBrand;
use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CatalogController extends Controller
{
    public function shop(Request $request)
    {
        $query = CatalogProduct::query()
            ->with(['media', 'category', 'brand'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains('name', $search)
                    ->orWhereJsonContains('description', $search)
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $category = CatalogCategory::where('slug', $request->string('category'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('brand')) {
            $brand = CatalogBrand::where('slug', $request->string('brand'))->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        if ($request->filled('badge')) {
            $query->whereJsonContains('badges', $request->string('badge'));
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        $categories = CatalogCategory::where('is_active', true)->orderBy('order')->get();
        $brands = CatalogBrand::where('is_active', true)->orderBy('order')->get();

        $categoryName = null;
        if ($request->filled('category')) {
            $cat = CatalogCategory::where('slug', $request->string('category'))->first();
            $categoryName = $cat?->name;
        }

        $brandName = null;
        if ($request->filled('brand')) {
            $b = CatalogBrand::where('slug', $request->string('brand'))->first();
            $brandName = $b?->name;
        }

        return view('pages.catalog.shop', compact('products', 'categories', 'brands', 'categoryName', 'brandName'));
    }

    public function show(CatalogProduct $product)
    {
        if (!$product->is_active || !$product->is_published) {
            abort(404);
        }

        $product->load(['media', 'category', 'tags', 'brand']);

        $related = CatalogProduct::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('media')
            ->limit(4)
            ->get();

        return view('pages.catalog.show', compact('product', 'related'));
    }

    public function quote()
    {
        return view('pages.catalog.quote');
    }
}
