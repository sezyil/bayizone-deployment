<?php

namespace App\Models\Catalog\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'language',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }
}
