<?php

namespace App\Http\Resources\Client;

use App\Enums\VariantTypesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\VariantValue $item */
        $item = $this->resource;
        //if has parameter
        $data = [
            'id' => $item->id,
            'variant_type' => $item->variant_type,
            'variant_type_label' => VariantTypesEnum::getDescription($item->variant_type, app()->getLocale()),
            'name' => $item->getName(),
            'is_default' => $item->is_default,
            'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
        ];
        if ($request->route()->hasParameter('id')) {
            $data['values'] = $item->values;
            $data['names'] = $item->getNames();
            $data['created_at'] = $item->created_at;
            $data['updated_at'] = $item->updated_at;
        }
        return $data;
    }
}
