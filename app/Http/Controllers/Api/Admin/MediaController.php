<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::query()
            ->when($request->search, fn($q, $search) => $q->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            }))
            ->when($request->type, fn($q, $type) => $q->byType($type))
            ->when($request->folder, fn($q, $folder) => $q->inFolder($folder))
            ->when($request->mime_type, fn($q, $mime) => $q->where('mime_type', $mime))
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate($request->per_page ?? 24);

        return MediaResource::collection($media);
    }

    public function store(StoreMediaRequest $request)
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $name = $request->name ?? pathinfo($originalName, PATHINFO_FILENAME);
        $fileName = uniqid() . '_' . time() . '.' . $extension;

        $path = $file->storeAs('media/originals', $fileName, 'public');
        $thumbnailPath = null;
        $width = null;
        $height = null;

        if (str_starts_with($mimeType, 'image/') && $extension !== 'svg') {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->decodeSplFileInfo($file);
                $width = $image->width();
                $height = $image->height();

                $thumbFileName = 'thumb_' . $fileName;
                $thumbPath = 'media/thumbnails/' . $thumbFileName;

                $thumbnail = $image->scaleDown(width: 300);
                Storage::disk('public')->put($thumbPath, $thumbnail->encode());
                $thumbnailPath = $thumbPath;
            } catch (\Exception $e) {
                // Thumbnail generation failed, proceed without it
            }
        }

        $media = Media::create([
            'name' => $name,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'disk' => 'public',
            'path' => 'media/originals/' . $fileName,
            'thumbnail_path' => $thumbnailPath,
            'alt_text' => $request->alt_text,
            'caption' => $request->caption,
            'description' => $request->description,
            'folder' => $request->folder,
            'width' => $width,
            'height' => $height,
            'created_by' => auth()->id(),
        ]);

        activity_log('media.created', [
            'media_id' => $media->id,
            'user_id' => auth()->id(),
        ]);

        return new MediaResource($media);
    }

    public function show(Media $medium)
    {
        return new MediaResource($medium);
    }

    public function update(UpdateMediaRequest $request, Media $medium)
    {
        $medium->update($request->validated());

        activity_log('media.updated', [
            'media_id' => $medium->id,
            'user_id' => auth()->id(),
        ]);

        return new MediaResource($medium);
    }

    public function destroy(Media $medium)
    {
        // Check if media is referenced by any model
        $references = $this->getReferences($medium);
        if (!empty($references)) {
            return response()->json([
                'message' => 'Cannot delete media. It is referenced by other records.',
                'references' => $references,
            ], 409);
        }

        // Delete files
        if (Storage::disk('public')->exists($medium->path)) {
            Storage::disk('public')->delete($medium->path);
        }
        if ($medium->thumbnail_path && Storage::disk('public')->exists($medium->thumbnail_path)) {
            Storage::disk('public')->delete($medium->thumbnail_path);
        }

        $medium->delete();

        activity_log('media.deleted', [
            'media_id' => $medium->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Media deleted successfully.']);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:media,id'],
        ]);

        $mediaItems = Media::whereIn('id', $request->ids)->get();
        $deleted = [];
        $blocked = [];

        foreach ($mediaItems as $medium) {
            $references = $this->getReferences($medium);
            if (!empty($references)) {
                $blocked[] = ['id' => $medium->id, 'name' => $medium->name, 'references' => $references];
                continue;
            }

            if (Storage::disk('public')->exists($medium->path)) {
                Storage::disk('public')->delete($medium->path);
            }
            if ($medium->thumbnail_path && Storage::disk('public')->exists($medium->thumbnail_path)) {
                Storage::disk('public')->delete($medium->thumbnail_path);
            }

            $medium->delete();
            $deleted[] = $medium->id;
        }

        if (!empty($blocked)) {
            return response()->json([
                'message' => 'Some media items were not deleted because they are referenced.',
                'deleted' => $deleted,
                'blocked' => $blocked,
            ], 200);
        }

        activity_log('media.bulk_deleted', [
            'ids' => $deleted,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Deleted successfully.',
            'deleted' => $deleted,
        ]);
    }

    public function folders()
    {
        $folders = Media::query()
            ->whereNotNull('folder')
            ->select('folder')
            ->distinct()
            ->orderBy('folder')
            ->pluck('folder');

        return response()->json(['data' => $folders]);
    }

    public function types()
    {
        $types = Media::query()
            ->selectRaw("CASE 
                WHEN mime_type LIKE 'image/%' THEN 'image'
                WHEN mime_type LIKE 'video/%' THEN 'video'
                WHEN mime_type LIKE 'audio/%' THEN 'audio'
                WHEN mime_type LIKE 'application/pdf' THEN 'pdf'
                WHEN mime_type LIKE 'application/msword' OR mime_type LIKE 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' THEN 'document'
                ELSE 'other'
            END as type_group")
            ->distinct()
            ->pluck('type_group');

        return response()->json(['data' => $types]);
    }

    protected function getReferences(Media $medium): array
    {
        $references = [];

        if ($medium->isImage()) {
            $services = \App\Models\Service::where('image_id', $medium->id)->count();
            if ($services > 0) {
                $references[] = ['model' => 'Service', 'count' => $services];
            }

            $projects = \App\Models\Project::where('image_id', $medium->id)->count();
            if ($projects > 0) {
                $references[] = ['model' => 'Project', 'count' => $projects];
            }

            $testimonials = \App\Models\Testimonial::where('image_id', $medium->id)->count();
            if ($testimonials > 0) {
                $references[] = ['model' => 'Testimonial', 'count' => $testimonials];
            }
        }

        return $references;
    }
}
