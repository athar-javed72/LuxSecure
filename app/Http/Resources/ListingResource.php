<?php

namespace App\Http\Resources;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Listing */
class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'location' => $this->location,
            'type' => $this->type_name,
            'listing_type_id' => $this->listing_type_id,
            'purpose' => $this->purpose,
            'price' => (float) $this->price,
            'price_unit' => $this->price_unit,
            'currency' => $this->currency,
            'country_id' => $this->country_id,
            'country' => $this->whenLoaded('country', fn () => [
                'id' => $this->country?->id,
                'iso2' => $this->country?->iso2,
                'name' => $this->country?->name,
                'currency_code' => $this->country?->currency_code,
            ]),
            'organization_id' => $this->organization_id,
            'branch_name' => $this->branch_name,
            'status' => $this->status,
            'primary_image_url' => $this->primary_image_url,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'attributes' => $this->attributeValues(),
            'description' => $this->when($request->routeIs('api.v1.listings.show'), $this->description),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => asset('storage/'.$img->path),
                'sort_order' => $img->sort_order,
            ])),
            'is_favorited' => $this->when(isset($this->is_favorited), (bool) $this->is_favorited),
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ];
    }
}
