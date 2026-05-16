<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\Localization\TranslationNamespace;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(): View
    {
        return view('pages.about', [
            'about' => TranslationNamespace::get('about'),
        ]);
    }
}
