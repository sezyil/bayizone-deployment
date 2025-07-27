<?php

namespace App\Models;

use App\Models\Catalog\Product\ProductVariant;
use App\Models\Catalog\Product\ProductVariantValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferRequestLineVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_request_line_id',
        'type',
        'product_variant_id',
        'product_variant_value_id',
    ];
    public function offer_request_line()
    {
        return $this->belongsTo(OfferRequestLine::class);
    }


    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product_variant_value()
    {
        return $this->belongsTo(ProductVariantValue::class);
    }

    /* Start: Scopes */
    //color
    public function scopeColors($query)
    {
        return $query->where('type', 'color');
    }

    //dimension
    public function scopeDimensions($query)
    {
        return $query->where('type', 'dimension');
    }
    /* End: Scopes */


    /* Start: Transform */
    //color
    public function transformData(bool $onlyValue = false, $withDetail = false)
    {
        if ($onlyValue) {
            $data = [
                'id' => $this->id,
                'type' => $this->type,
                'product_variant_id' => $this->product_variant_id,
                'product_variant_value_id' => $this->product_variant_value_id,
                'variant_id' => $this->product_variant?->variant_id ?? null,
                'variant_value_id' => $this->product_variant_value?->variant_value_id ?? $this->product_variant_value->id
            ];

            if ($withDetail) {
                $data['variant'] = $this->product_variant ? [
                    'id' => $this->product_variant->id,
                    'name' => $this->product_variant->getName(),
                    'variant_id' => $this->product_variant->variant_id,
                ] : null;
                $data['variant_value'] = $this->product_variant_value ? [
                    'id' => $this->product_variant_value->id,
                    'name' => $this->product_variant_value->getName(),
                    'variant_value_id' => $this->product_variant_value->variant_value_id,
                    'value' => $this->product_variant_value->getJsonValue()
                ] : null;
            }
            return $data;
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
    /* End: Transform */
}
