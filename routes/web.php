<?php

use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CatalogQuoteController;
use App\Http\Controllers\Public\BookingController as PublicBookingController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LocaleController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\ProjectController;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [AboutController::class, 'index'])->name('public.about');

Route::get('/robots.txt', function () {
    $disallow = app()->environment('production') ? '' : 'Disallow: /';
    $sitemap = route('sitemap');

    return response("User-agent: *\n{$disallow}\nAllow: /\nSitemap: {$sitemap}\n")
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $servicesEnabled = module_enabled('services');
    $projectsEnabled = module_enabled('projects');
    $blogEnabled = module_enabled('blog');
    $contactEnabled = module_enabled('contact');

    $services = $servicesEnabled
        ? \App\Models\Service::query()->where('is_active', true)->get(['id', 'slug', 'updated_at'])
        : collect();
    $projects = $projectsEnabled
        ? \App\Models\Project::query()->where('is_active', true)->get(['id', 'slug', 'updated_at'])
        : collect();
    $blogPosts = $blogEnabled
        ? \App\Models\BlogPost::query()->where('is_published', true)->get(['id', 'slug', 'updated_at'])
        : collect();

    $urls = [
        ['loc' => route('home'), 'priority' => '1.0', 'lastmod' => now()->toDateString()],
        ['loc' => route('public.about'), 'priority' => '0.8'],
    ];

    if ($servicesEnabled) {
        $urls[] = ['loc' => route('public.services.index'), 'priority' => '0.9'];
    }

    if ($projectsEnabled) {
        $urls[] = ['loc' => route('public.projects.index'), 'priority' => '0.9'];
    }

    if ($blogEnabled) {
        $urls[] = ['loc' => route('public.blog.index'), 'priority' => '0.9'];
    }

    if ($contactEnabled) {
        $urls[] = ['loc' => route('public.contact'), 'priority' => '0.7'];
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $url) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$url['loc']}</loc>\n";
        $xml .= "    <priority>{$url['priority']}</priority>\n";
        if (isset($url['lastmod'])) {
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
        }
        $xml .= "  </url>\n";
    }

    foreach ($services as $service) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . route('public.services.show', $service) . "</loc>\n";
        $xml .= "    <priority>0.7</priority>\n";
        $xml .= "    <lastmod>{$service->updated_at->toDateString()}</lastmod>\n";
        $xml .= "  </url>\n";
    }

    foreach ($projects as $project) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . route('public.projects.show', $project) . "</loc>\n";
        $xml .= "    <priority>0.7</priority>\n";
        $xml .= "    <lastmod>{$project->updated_at->toDateString()}</lastmod>\n";
        $xml .= "  </url>\n";
    }

    foreach ($blogPosts as $post) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . route('public.blog.show', $post) . "</loc>\n";
        $xml .= "    <priority>0.7</priority>\n";
        $xml .= "    <lastmod>{$post->updated_at->toDateString()}</lastmod>\n";
        $xml .= "  </url>\n";
    }

    $xml .= '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::middleware('module:contact')->group(function () {
    Route::get('/contact', [ContactController::class, 'index'])->name('public.contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('public.contact.store');
});

Route::middleware('module:services')->group(function () {
    Route::get('/services', [ServiceController::class, 'index'])->name('public.services.index');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('public.services.show');
});

Route::middleware('module:projects')->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('public.projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('public.projects.show');
});

Route::middleware('module:blog')->group(function () {
    Route::get('/blog', [BlogController::class, 'index'])->name('public.blog.index');
    Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('public.blog.show');
});

Route::middleware('module:catalog')->group(function () {
    Route::get('/shop', [CatalogController::class, 'shop'])->name('public.catalog.shop');
    Route::get('/catalog', [CatalogController::class, 'shop'])->name('public.catalog.index');
    Route::get('/catalog/{product}', [CatalogController::class, 'show'])->name('public.catalog.show');
    Route::get('/catalog/quote', [CatalogController::class, 'quote'])->name('public.catalog.quote');
    Route::post('/catalog/quote', [CatalogQuoteController::class, 'store'])->name('public.catalog.quote.store');
});

Route::middleware('module:booking')->group(function () {
    Route::get('/bookings', [PublicBookingController::class, 'index'])->name('public.booking.index');
    Route::post('/bookings', [PublicBookingController::class, 'store'])->name('public.booking.store');
    Route::get('/bookings/availability/{serviceId}', [PublicBookingController::class, 'availability'])->name('public.booking.availability');
});

Route::view('/auth/login', 'app')->name('auth.login');
Route::view('/auth/register', 'app')->name('auth.register');

Route::view('/admin', 'app')->name('admin');
Route::get('/auth/google', [\App\Http\Controllers\Public\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Public\Auth\SocialAuthController::class, 'handleGoogleCallback']);

Route::view('/admin/{path}', 'app')->where('path', '.*');
