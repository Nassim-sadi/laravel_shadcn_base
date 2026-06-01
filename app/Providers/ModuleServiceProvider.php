<?php

namespace App\Providers;

use App\Support\Modules\ModuleRegistry;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modules = app(ModuleRegistry::class)->resolve();

        view()->share('modules', $modules);
    }
}
