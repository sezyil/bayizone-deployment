<?php

namespace App\Http\Resources\Client\Shipment;

use App\Libraries\Response\DatatableResponder;
use App\Models\CustomerShipment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipmentListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $collection = $this->collection;

        $data = $collection->transform(function ($item) {
            /** @var CustomerShipment $item */
            return [
                'id' => $item->id,
                'company_customer_id' => $item->company_customer_id,
                'company_customer_name' => $item->company_customer_name,
                'shipment_no' => $item->shipment_no,
                'shipment_date' => $item->shipment_date,
                'shipment_type' => $item->shipment_type,
                'currency' => $item->currency,
                'currency_label' => $item->getCurrency->title,
                'status' => $item->status,
                'status_label' => $item->statusLabel(),
                'created_at' => $item->created_at->format('d.m.Y H:i'),
                'total_weight' => $item->total_weight,
                'total_volume' => $item->total_volume . ' m³',
                'total_package' => $item->total_package,
            ];
        });

        return $data->toArray();
    }
}
