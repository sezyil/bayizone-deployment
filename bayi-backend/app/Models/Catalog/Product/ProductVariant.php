<?php

namespace App\Models\Catalog\Product;

use App\Models\Catalog\Product\Product;
use App\Models\Customer\CustomerOfferLineVariant;
use App\Models\Customer\CustomerOrderLineVariant;
use App\Models\OfferRequestLineVariant;
use App\Models\StoreUserCartItemVariant;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'product_id',
        'type',
        'variant_id',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    //boot
    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($model) {
            /** @var ProductVariant $model */
            $model->variantValues()->delete();
            $model->orderLineVariants()->delete();
            $model->offerLineVariants()->delete();
            $model->storeUserCartItemVariants()->delete();
            $model->offerRequestLineVariants()->delete();
        });
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //variant
    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_id');
    }

    //value
    public function variantValues()
    {
        return $this->hasMany(ProductVariantValue::class);
    }


    //orderline
    public function orderLineVariants()
    {
        return $this->hasMany(CustomerOrderLineVariant::class);
    }

    //offerline
    public function offerLineVariants()
    {
        return $this->hasMany(CustomerOfferLineVariant::class);
    }

    //storeCartItem
    public function storeUserCartItemVariants()
    {
        return $this->hasMany(StoreUserCartItemVariant::class);
    }

    //offerrequestline
    public function offerRequestLineVariants()
    {
        return $this->hasMany(OfferRequestLineVariant::class);
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

    //name
    public function getName()
    {
        return $this->variant?->getName() ?? null;
    }
}
