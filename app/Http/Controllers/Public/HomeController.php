<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'services' => $this->moduleContent('services', Service::class, 6),
            'projects' => $this->moduleContent('projects', Project::class, 6),
            'testimonials' => $this->moduleContent('testimonials', Testimonial::class, 6),
            'faqs' => $this->moduleContent('faqs', Faq::class, 8),
        ]);
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function moduleContent(string $module, string $model, int $limit): Collection
    {
        if (! config("modules.{$module}", true)) {
            return collect();
        }

        return $this->activeContent($model, $limit);
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function activeContent(string $model, int $limit): Collection
    {
        $instance = new $model;

        if (! Schema::hasTable($instance->getTable())) {
            return collect();
        }

        return $model::query()->active()->ordered()->limit($limit)->get();
    }
}
