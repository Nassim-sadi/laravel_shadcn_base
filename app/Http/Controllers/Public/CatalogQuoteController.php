<?php

namespace App\Http\Controllers\Public;

use App\Http\Requests\Api\StoreQuoteRequestRequest;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class CatalogQuoteController extends Controller
{
    public function store(StoreQuoteRequestRequest $request): RedirectResponse
    {
        QuoteRequest::create($request->validated());

        return back()->with('success', __('catalog.quoteSuccess'));
    }
}
