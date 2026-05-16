<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()->active()->ordered()->paginate(12);

        return view('pages.services.index', compact('services'));
    }

    public function show(Service $service): View
    {
        if (! $service->is_active) {
            abort(404);
        }

        return view('pages.services.show', compact('service'));
    }
}
