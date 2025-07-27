<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    const CODE_LENGTH = 20;
    protected $fillable = [
        'name',
        'code'
    ];
}
