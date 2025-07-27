<?php

namespace App\Models;

use App\Models\Catalog\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreUserViewedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_user_id',
        'product_uuid'
    ];

    public function storeUser()
    {
        return $this->belongsTo(StoreUser::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_uuid', 'uuid');
    }
}
