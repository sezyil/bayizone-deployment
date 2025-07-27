<?php

namespace App\Models;

use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\System\Language;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'type',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    //product
    public function products()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function values()
    {
        return $this->hasMany(VariantValue::class, 'variant_type', 'type');
    }

    public function descriptions()
    {
        return $this->hasMany(VariantDescription::class);
    }

    public function description($language = null)
    {
        if ($language) {
            $description = $this->descriptions->where('language', $language)->first();
            if ($description) {
                return $description;
            }
        }

        return $this->descriptions->first();
    }

    //get keys lang based
    public function getNames()
    {
        $names = [];
        $descriptions = $this->descriptions;
        foreach (Language::all() as $language) {
            $description = $descriptions->where('language', $language->code)->first();
            $names[$language->code] = null;
            if ($description) {
                $names[$language->code] = $description->name;
            }
        }

        return $names;
    }

    //get name
    public function getName($language = null)
    {
        $_lang = app()->getLocale();
        if ($language) {
            $_lang = $language;
        }
        $description = $this->description($_lang);
        if ($description) {
            /** @var VariantDescription $description */
            return $description->name;
        } else {
            return $this->descriptions?->first()?->name ?? null;
        }
    }

    #region Scopes
    //default
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeNotDefault($query)
    {
        return $query->where('is_default', false);
    }

    //active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    //default and customer
    public function scopeDefaultAndCustomer($query, $customerId)
    {
        //is default or customer_id
        return $query->where(function ($query) use ($customerId) {
            $query->where('is_default', true)
                ->orWhere('customer_id', $customerId);
        });
    }
    #endregion
}
