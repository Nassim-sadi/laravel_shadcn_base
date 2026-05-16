<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()->active()->ordered()->paginate(12);

        return view('pages.projects.index', compact('projects'));
    }

    public function show(Project $project): View
    {
        if (! $project->is_active) {
            abort(404);
        }

        return view('pages.projects.show', compact('project'));
    }
}
