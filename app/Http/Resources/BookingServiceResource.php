<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->translated('name'),
            'name_translations' => $this->name,
            'description' => $this->translated('description'),
            'description_translations' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'availability_rules' => $this->whenLoaded('availabilityRules', function () {
                return $this->availabilityRules->map(function ($rule) {
                    return [
                        'id' => $rule->id,
                        'day_of_week' => $rule->day_of_week,
                        'start_time' => $rule->start_time,
                        'end_time' => $rule->end_time,
                        'is_active' => $rule->is_active,
                    ];
                });
            }),
            'time_blocks' => $this->whenLoaded('timeBlocks', function () {
                return $this->timeBlocks->map(function ($block) {
                    return [
                        'id' => $block->id,
                        'date' => $block->date,
                        'start_time' => $block->start_time,
                        'end_time' => $block->end_time,
                        'type' => $block->type,
                        'reason' => $block->reason,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
