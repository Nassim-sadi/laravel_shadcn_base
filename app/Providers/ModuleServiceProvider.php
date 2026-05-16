<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $modules = $this->getActiveModules();

        view()->share('activeModules', $modules);

        foreach ($modules as $module) {
            $routeFile = base_path("routes/modules/{$module}.php");
            if (file_exists($routeFile)) {
                $this->loadRoutesFrom($routeFile);
            }
        }
    }

    public function getActiveModules(): array
    {
        return collect(config('modules', []))
            ->filter(fn ($enabled) => $enabled)
            ->keys()
            ->values()
            ->all();
    }
}
