<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;


    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany(Cities::class, 'state_id', 'id');
    }
}
