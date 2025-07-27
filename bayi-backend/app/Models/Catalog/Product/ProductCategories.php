<?php

namespace App\Models\Catalog\Product;

use App\Models\Catalog\Category\Categories;
use App\Models\Catalog\Category\CategoryDescription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }

    public function description()
    {
        return $this->hasOne(CategoryDescription::class, 'category_id', 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
