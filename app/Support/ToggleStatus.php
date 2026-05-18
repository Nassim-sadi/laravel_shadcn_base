<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait ToggleStatus
{
    /**
     * Toggle the is_active/is_published status of a model.
     */
    protected function doToggleStatus(Model $model): JsonResponse
    {
        $this->authorize('update', $model);

        $statusColumn = $this->resolveStatusColumn($model);

        if ($statusColumn === null) {
            throw ValidationException::withMessages([
                'status' => 'This model does not support status toggling.',
            ]);
        }

        $model->{$statusColumn} = !$model->{$statusColumn};
        $model->save();

        activity_log('status.toggled', [
            'model' => get_class($model),
            'model_id' => $model->id,
            'user_id' => auth()->id(),
            'new_status' => $model->{$statusColumn},
        ]);

        return response()->json([
            'message' => 'Status updated successfully.',
            'data' => [
                $statusColumn => $model->{$statusColumn},
            ],
        ]);
    }

    /**
     * Resolve the status column name for the model.
     */
    protected function resolveStatusColumn(Model $model): ?string
    {
        if (array_key_exists('is_active', $model->getAttributes())) {
            return 'is_active';
        }

        if (array_key_exists('is_published', $model->getAttributes())) {
            return 'is_published';
        }

        if (array_key_exists('is_read', $model->getAttributes())) {
            return 'is_read';
        }

        return null;
    }
}
