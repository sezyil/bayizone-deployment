<?php

namespace App\Models;

use App\Models\System\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantValueDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_value_id',
        'name',
        'language',
    ];

    public function variantValue()
    {
        return $this->belongsTo(VariantValue::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
