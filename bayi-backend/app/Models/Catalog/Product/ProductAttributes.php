<?php

namespace App\Models\Catalog\Product;

use App\Models\Catalog\Attribute\Attributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributes extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'language',
        'text',
    ];

    public function parent()
    {
        return  $this->belongsTo(Attributes::class, 'attribute_id');
    }
}
