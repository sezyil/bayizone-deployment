<?php

namespace App\Http\Resources\Client\Shipment;

use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipmentAvailableOrdersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $collection = $this->collection;

        $data = $collection->map(function ($item) {
            /** @var CustomerOrder $item */
            $tmp = [
                'order_id' => $item->id,
                'order_no' => $item->order_no,
                'order_date' => $item->order_date,
                'items' => $item->lines?->map(function ($line) {
                    /** @var CustomerOrderLine $line */
                    $color = $line->variants()->colors()->get()->transform(function ($v) {
                        return $v->transformData(true,true);
                    }) ?? null;
                    $dimension = $line->variants()->dimensions()->get()->transform(function ($v) {
                        return $v->transformData(true,true);
                    }) ?? null;
                    return [
                        'line_id' => $line->id,
                        'product_id' => $line->product_id,
                        'product_name' => $line->product?->getName() ?? $line->product_name,
                        'product_image' => $line->product?->image ?? null,
                        'quantity' => $line->quantity,
                        'unit_price' => $line->unit_price,
                        'total_price' => $line->total_price,
                        'total_tax' => $line->total_tax,
                        'total_discount' => $line->total_discount,
                        'grand_total' => $line->grand_total,
                        'unit_volume' => $line->unit_volume,
                        'unit_package' => $line->unit_package,
                        'total_volume' => $line->total_volume,
                        'total_package' => $line->total_package,
                        'color' => $color,
                        'dimension' => $dimension,
                        'remaining_quantity' => $line->remaining_quantity,
                        'sent_quantity' => $line->sent_quantity,
                    ];
                }),
            ];
            return $tmp;
        });

        return $data->toArray();
    }
}
