<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::query()
            ->with('image')
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhere('client', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->client, fn($q, $client) => $q->where('client', $client))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new ProjectCollection($projects);
    }

    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'project_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('projects', $filename, 'public');
            $validated['image'] = $path;
        }

        $project = Project::create($validated);

        // Log activity
        activity_log('project.created', [
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);

        return new ProjectResource($project->load('image'));
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return new ProjectResource($project->load('image'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }
            
            $file = $request->file('image');
            $filename = 'project_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('projects', $filename, 'public');
            $validated['image'] = $path;
        }

        $project->update($validated);

        // Log activity
        activity_log('project.updated', [
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);

        return new ProjectResource($project->load('image'));
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // Delete associated image
        if ($project->image && Storage::disk('public')->exists($project->image)) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        // Log activity
        activity_log('project.deleted', [
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
