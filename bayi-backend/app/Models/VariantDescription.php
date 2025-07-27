<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'language',
        'name',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
