<?php

namespace App\Models\Catalog\Product;

use App\Enums\VariantTypesEnum;
use App\Models\VariantValue;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariantValue extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'product_variant_id',
        'variant_value_id',
        'type',
        'value', //nullable
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function variantValue()
    {
        return $this->belongsTo(VariantValue::class, 'variant_value_id');
    }

    //get name
    public function getName()
    {
        return $this->variantValue?->getName() ?? null;
    }

    //get value only color
    public function getJsonValue()
    {
        if ($this->type !== VariantTypesEnum::DIMENSION->value) {
            return null;
        }
        return $this->value;
    }
}
