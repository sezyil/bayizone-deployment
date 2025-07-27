<?php

namespace App\Models\Catalog\Product;

use App\Models\Catalog\Attribute\Attributes;

use App\Models\Customer;
use App\Models\Customer\CustomerOfferLine;
use App\Models\OrderLine;
use App\Models\StoreUserCartItem;
use App\Models\System\Currency;
use App\Models\System\Unit;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    const MODEL_MIN_LENGTH = 6;
    const MODEL_MAX_LENGTH = 20;
    const NAME_MIN_LENGTH = 6;
    const NAME_MAX_LENGTH = 120;
    protected $hidden = [
        'customer_id'
    ];

    protected $fillable = [
        'customer_id',
        'model',
        'sku',
        'upc',
        'ean',
        'jan',
        'isbn',
        'mpn',
        'image',
        'quantity',
        'unit_id',
        'price_tl',
        'price_usd',
        'price_euro',
        'price_gbp',
        'default_currency',
        'date_available',
        'weight',
        'length',
        'width',
        'height',
        'volume',
        'package',
        'minimum',
        'status',
        'store_visibility',
        'active_customer_group',
        'ai_sync',
        'ai_last_sync'
    ];

    //uuid
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::uuid();
        });

        //if active_customer_group is null set it to empty array
        static::saving(function ($model) {
            if ($model->active_customer_group == null) {
                $model->active_customer_group = [];
            }
        });
        //if getting active_customer_group is null set it to empty array
        static::retrieved(function ($model) {
            if ($model->active_customer_group == null) {
                $model->active_customer_group = [];
            }
        });
    }

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'minimum' => 'integer',
        'store_visibility' => 'boolean',
        'active_customer_group' => 'array',
        'ai_sync' => 'boolean'
    ];

    //scope for active products
    public function scopeActive(Builder $query)
    {
        return $query->where('status', true);
    }

    //scope for inactive products
    public function scopeInactive(Builder $query)
    {
        return $query->where('status', false);
    }

    //scope for store visibility
    public function scopeStoreVisibility(Builder $query)
    {
        return $query->where('store_visibility', true);
    }

    //scope for not store visibility
    public function scopeNotStoreVisibility(Builder $query)
    {
        return $query->where('store_visibility', false);
    }

    //scope for active customer group
    public function scopeActiveCustomerGroup(Builder $query, string $company_customer_group)
    {
        return $query->whereJsonContains('active_customer_group', $company_customer_group);
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    //variant
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function description()
    {
        return $this->hasOne(ProductDescription::class)->where('language', app()->getLocale());
    }

    public function descriptions()
    {
        return $this->hasMany(ProductDescription::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    //get name
    public function getName()
    {
        //if description is not null return description name else return descriptions name
        return $this->description ? $this->description->name : $this->descriptions()->first()->name ?? '---';
    }

    //get description
    public function getDescription()
    {
        //if description is not null return description description else return descriptions description
        return $this->description ? $this->description->description : $this->descriptions()->first()->description ?? '---';
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttributes::class);
    }
    public function categories()
    {
        return $this->hasMany(ProductCategories::class);
    }

    //currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency', 'code');
    }


    public function orderLines()
    {
        return $this->hasMany(OrderLine::class, 'product_id');
    }

    public function storeCartItems()
    {
        return $this->hasMany(StoreUserCartItem::class, 'product_uuid');
    }

    //offers
    public function offers()
    {
        return $this->hasMany(CustomerOfferLine::class, 'product_id');
    }
    public function triggerImageChange()
    {
        $this->image = $this->images()->first()->image;
        $this->save();
        //find offers and update image
        $offers = $this->offers()->get();
        foreach ($offers as $offer) {
            /** @var CustomerOfferLine $offer */
            $offer->product_image_url = $this->image;
            $offer->save();
        }
    }

    public function activeProducts()
    {
        return $this->where('status', '=', true);
    }

    //sales count of product
    public function salesCount()
    {
        //offer and sales count calculation sum of quantity morphed to customer offer line and order line
        return $this->offers()->sum('quantity') + $this->orderLines()->sum('quantity');
    }

    //default currency price
    public function getDefaultPriceAttribute()
    {
        $currency = Currency::where('code', $this->default_currency)->first();
        $price = $this->{"price_" . strtolower($this->default_currency)};
        return $currency->format($price, true);
    }

    public function getStoreUrl()
    {
        return  env('CLIENT_STORE_URL') . '/' . $this->customer_id . '/product/' . $this->uuid;
    }

    public function getThumbWithUrl()
    {
        return env('APP_URL') . '/' . $this->image;
    }
}
