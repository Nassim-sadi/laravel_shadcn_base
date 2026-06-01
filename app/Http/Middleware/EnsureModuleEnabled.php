<?php

namespace App\Http\Middleware;

use App\Support\Modules\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! app(ModuleRegistry::class)->isEnabled($module)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => __('This feature is not available.'),
                ], 403);
            }

            abort(404);
        }

        return $next($request);
    }
}
