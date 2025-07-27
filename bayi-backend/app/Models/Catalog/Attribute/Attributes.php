<?php

namespace App\Models\Catalog\Attribute;

use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attributes extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'attribute_group_id',
    ];

    public function group()
    {
        return $this->belongsTo(AttributeGroup::class, 'attribute_group_id');
    }

    /**
     * Translated Group Name
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function groupDescription()
    {
        return $this->hasOne(AttributeGroupDescription::class, 'attribute_group_id', 'attribute_group_id')->where('language', app()->getLocale());
    }

    public function description()
    {
        return $this->hasOne(AttributeDescription::class, 'attribute_id')->where('language', app()->getLocale());
    }

    public function descriptions()
    {
        return $this->hasMany(AttributeDescription::class, 'attribute_id');
    }

    public function hasProduct()
    {
        return $this->hasMany(ProductAttributes::class, 'attribute_id');
    }
}
