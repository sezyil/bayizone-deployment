<?php

namespace App\Models\Catalog\Attribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroupDescription extends Model
{
    protected $fillable = [
        'attribute_group_id',
        'language',
        'name',
    ];


    public function option()
    {
        return $this->belongsTo(AttributeGroup::class);
    }
}
