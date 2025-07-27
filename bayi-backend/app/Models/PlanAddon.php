<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanAddon extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'name',
        'quantity',
        'price',
        'status',
        'is_bulk',
        'bulk_quantity',
        'is_boolean',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_bulk' => 'boolean',
        'is_boolean' => 'boolean',
    ];
}
