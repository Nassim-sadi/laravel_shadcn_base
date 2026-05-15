<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $projects = Project::query()
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhere('client', 'like', "%{$search}%"))
            ->when($request->is_active !== null, fn($q) => $q->where('is_active', $request->is_active))
            ->when($request->client, fn($q, $client) => $q->where('client', $client))
            ->orderBy($request->sort_by ?? 'order', $request->sort_order ?? 'asc')
            ->paginate($request->per_page ?? 15);

        return new ProjectCollection($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'url' => 'nullable|url|max:255',
            'technologies' => 'sometimes|array',
            'technologies.*' => 'string',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);

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

        return new ProjectResource($project);
    }

    public function show(Project $project)
    {
        return new ProjectResource($project);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'client' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'url' => 'sometimes|url|max:255',
            'technologies' => 'sometimes|array',
            'technologies.*' => 'string',
            'order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);

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

        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
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