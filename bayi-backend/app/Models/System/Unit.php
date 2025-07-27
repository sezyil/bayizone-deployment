<?php

namespace App\Models\System;

use App\Models\Catalog\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'short_name',
        'is_system',
        'is_active',
        'customer_id'
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function descriptions()
    {
        return $this->hasMany(UnitDescription::class);
    }

    public function description($language = null)
    {
        if ($language) {
            return $this->hasOne(UnitDescription::class)->where('language', $language);
        }

        return $this->hasOne(UnitDescription::class)->where('language', app()->getLocale());
    }
}
