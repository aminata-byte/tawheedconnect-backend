<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'city' => $this->city,

            // ✅ FORMAT MACHINE (IMPORTANT POUR FLUTTER)
            'start_date' => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date'   => $this->end_date?->format('Y-m-d H:i:s'),

            // ✅ FORMAT UI (OPTIONNEL)
            'date' => $this->start_date?->format('Y-m-d'),
            'start_time' => $this->start_date?->format('H:i'),
            'end_time' => $this->end_date?->format('H:i'),

            'status' => $this->status,
            'organizers' => $this->organizers,
            'image' => $this->image ? asset('storage/' . $this->image) : null,

            'participants_count' => $this->participants_count,
            'views_count' => $this->views_count,
            'shares_count' => $this->shares_count,

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
