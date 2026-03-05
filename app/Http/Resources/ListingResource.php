<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'property_type' => $this->property_type,
            'neighborhood' => $this->neighborhood,
            'location' => [
                'address' => $this->location_address,
                'latitude' => (float) $this->location_lat,
                'longitude' => (float) $this->location_long,
            ],
            'verification_status' => $this->verification_status,
            'is_available' => $this->is_available,
            'view_count' => $this->view_count,
            'photo' => new PhotoResource($this->primaryPhoto),
            'photos' => PhotoResource::collection($this->photos),
            'landlord' => [
                'id' => $this->landlord->id,
                'name' => $this->landlord->name,
                'phone_number' => $this->landlord->phone_number,
            ],
            'whatsapp_link' => $this->whats_app_link,
            'is_favorited' => $request->user() ? $this->isFavoritedBy($request->user()->id) : false,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
