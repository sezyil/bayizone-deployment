<?php

namespace App\Models\Catalog\Attribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeDescription extends Model
{

    protected $fillable = [
        'attribute_id',
        'language',
        'name',
    ];

    public function attribute()
    {
        return $this->belongsTo(AttributeGroup::class);
    }
}
