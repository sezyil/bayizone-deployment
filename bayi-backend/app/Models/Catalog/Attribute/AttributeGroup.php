<?php

namespace App\Models\Catalog\Attribute;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{

    public function description(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AttributeGroupDescription::class, 'attribute_group_id')->where('language', app()->getLocale());
    }

    //with lang
    public function descriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttributeGroupDescription::class);
    }

    public function attributes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attributes::class, 'attribute_group_id');
    }
}
