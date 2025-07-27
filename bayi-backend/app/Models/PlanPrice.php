<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'month',
    ];

    protected $casts = [
        'price' => 'float',
        'month' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    //is yearly
    public function isYearly()
    {
        return $this->month == 12;
    }
}
