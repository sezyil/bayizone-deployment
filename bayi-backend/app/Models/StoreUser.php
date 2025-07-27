<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreUser extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function viewedProducts()
    {
        return $this->hasMany(StoreUserViewedProduct::class);
    }

    public function cartItems()
    {
        return $this->hasMany(StoreUserCartItem::class, 'store_user_id');
    }
}
