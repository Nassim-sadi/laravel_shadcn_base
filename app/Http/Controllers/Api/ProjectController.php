<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use App\Models\Project;
use App\Support\ToggleStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    use ToggleStatus;
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

            $media = \App\Models\Media::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'thumbnail_path' => null,
                'created_by' => auth()->id(),
            ]);

            $validated['image_id'] = $media->id;
        }

        $project = Project::create($validated);

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
            if ($project->image_id) {
                $oldMedia = \App\Models\Media::find($project->image_id);
                if ($oldMedia) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldMedia->path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldMedia->path);
                    }
                    if ($oldMedia->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldMedia->thumbnail_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldMedia->thumbnail_path);
                    }
                    $oldMedia->delete();
                }
            }

            $file = $request->file('image');
            $filename = 'project_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('projects', $filename, 'public');

            $media = \App\Models\Media::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
                'thumbnail_path' => null,
                'created_by' => auth()->id(),
            ]);

            $validated['image_id'] = $media->id;
            unset($validated['image']);
        }

        $project->update($validated);

        activity_log('project.updated', [
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);

        return new ProjectResource($project->load('image'));
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        if ($project->image_id) {
            $media = \App\Models\Media::find($project->image_id);
            if ($media) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($media->path);
                }
                if ($media->thumbnail_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($media->thumbnail_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($media->thumbnail_path);
                }
                $media->delete();
            }
        }

        $project->delete();

        activity_log('project.deleted', [
            'project_id' => $project->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Project deleted successfully']);
    }

    public function toggleStatus(Project $project): JsonResponse
    {
        return $this->doToggleStatus($project);
    }
}
