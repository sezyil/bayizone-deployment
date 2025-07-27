<?php

namespace App\Models;

use App\Models\Catalog\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferRequestLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_request_id',
        'product_id',
        'quantity',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function offerRequest()
    {
        return $this->belongsTo(OfferRequest::class, 'offer_request_id', 'id');
    }

    public function variants()
    {
        return $this->hasMany(OfferRequestLineVariant::class, 'offer_request_line_id');
    }
}
