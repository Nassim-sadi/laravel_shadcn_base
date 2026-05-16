<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('public.about');

Route::get('/robots.txt', function () {
    $disallow = app()->environment('production') ? '' : 'Disallow: /';

    return response(<<<TXT
User-agent: *
{$disallow}
Allow: /
Sitemap: {{ url('/sitemap.xml') }}
TXT)->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'priority' => '1.0'],
        ['loc' => route('public.services.index'), 'priority' => '0.9'],
        ['loc' => route('public.projects.index'), 'priority' => '0.9'],
        ['loc' => route('public.about'), 'priority' => '0.8'],
        ['loc' => route('public.contact'), 'priority' => '0.7'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $url) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$url['loc']}</loc>\n";
        $xml .= "    <priority>{$url['priority']}</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/contact', [ContactController::class, 'index'])->name('public.contact');
Route::post('/contact', [ContactController::class, 'store'])->name('public.contact.store');

Route::get('/services', [ServiceController::class, 'index'])->name('public.services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('public.services.show');

Route::get('/projects', [ProjectController::class, 'index'])->name('public.projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('public.projects.show');

Route::view('/auth/login', 'app')->name('login');
Route::view('/auth/register', 'app')->name('register');

Route::view('/admin', 'app')->name('admin');
Route::view('/admin/{path}', 'app')->where('path', '.*');
