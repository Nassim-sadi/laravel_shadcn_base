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
    $sitemap = route('sitemap');

    return response("User-agent: *\n{$disallow}\nAllow: /\nSitemap: {$sitemap}\n")
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $servicesEnabled = Route::has('public.services.index') && Route::has('public.services.show');
    $projectsEnabled = Route::has('public.projects.index') && Route::has('public.projects.show');
    $contactEnabled = Route::has('public.contact');

    $services = $servicesEnabled
        ? \App\Models\Service::query()->where('is_active', true)->get(['id', 'slug', 'updated_at'])
        : collect();
    $projects = $projectsEnabled
        ? \App\Models\Project::query()->where('is_active', true)->get(['id', 'slug', 'updated_at'])
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

    $xml .= '</urlset>';

    return response($xml)->header('Content-Type', 'application/xml');
})->name('sitemap');

if (config('modules.contact', true)) {
    Route::get('/contact', [ContactController::class, 'index'])->name('public.contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('public.contact.store');
}

if (config('modules.services', true)) {
    Route::get('/services', [ServiceController::class, 'index'])->name('public.services.index');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('public.services.show');
}

if (config('modules.projects', true)) {
    Route::get('/projects', [ProjectController::class, 'index'])->name('public.projects.index');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('public.projects.show');
}

Route::view('/auth/login', 'app')->name('auth.login');
Route::view('/auth/register', 'app')->name('auth.register');

Route::view('/admin', 'app')->name('admin');
Route::view('/admin/{path}', 'app')->where('path', '.*');
