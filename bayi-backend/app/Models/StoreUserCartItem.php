<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreUserCartItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_user_id',
        'product_uuid',
        'quantity',
    ];

    public function storeUser()
    {
        return $this->belongsTo(StoreUser::class);
    }

    public function variants()
    {
        return $this->hasMany(StoreUserCartItemVariant::class);
    }
}
