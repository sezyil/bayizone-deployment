<?php

namespace App\Http\Resources\Client;

use App\Enums\VariantTypesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //if withValues
        $withValues = $request->query('withValues', false);
        /** @var \App\Models\Variant $item */
        $item = $this->resource;
        //if has parameter
        $data = [
            'id' => $item->id,
            'type' => $item->type,
            'type_label' => VariantTypesEnum::getDescription($item->type, app()->getLocale()),
            'name' => $item->getName(),
            'is_active' => $item->is_active,
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

        if ($withValues) {
            $data['values'] = $item->values ? new VariantValueCollection($item->values) : [];
        }

        return $data;
    }
}
