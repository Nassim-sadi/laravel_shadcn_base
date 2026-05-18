<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'locale' => 'required|string|in:fr,en,ar',
            'redirect' => 'nullable|string',
        ]);

        session(['locale' => $validated['locale']]);

        return redirect($validated['redirect'] ?? back());
    }
}
