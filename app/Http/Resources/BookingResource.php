<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_service_id' => $this->booking_service_id,
            'service' => $this->whenLoaded('service', fn () => new BookingServiceResource($this->service)),
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'notes' => $this->notes,
            'status' => $this->status,
            'confirmations' => $this->whenLoaded('confirmations', function () {
                return $this->confirmations->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'action' => $c->action,
                        'notes' => $c->notes,
                        'user' => $c->user?->name,
                        'created_at' => $c->created_at,
                    ];
                });
            }),
            'reschedules' => $this->whenLoaded('reschedules', function () {
                return $this->reschedules->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'old_date' => $r->old_date,
                        'old_start_time' => $r->old_start_time,
                        'new_date' => $r->new_date,
                        'new_start_time' => $r->new_start_time,
                        'reason' => $r->reason,
                        'created_at' => $r->created_at,
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
