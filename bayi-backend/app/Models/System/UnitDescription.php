<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'language',
        'name',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
