<?php

namespace App\Http\Resources\Client;

use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VariantCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($variant) use ($request) {
            /** @var Variant $variant */
            return new VariantResource($variant);
        })->toArray();
    }
}
