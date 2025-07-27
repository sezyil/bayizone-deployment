<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    use HasFactory;

    public function states()
    {
        return $this->hasMany(States::class, 'country_id', 'id');
    }

    public function translateName($autoConvert = false)
    {
        $name = $this->name;
        $translations = json_decode($this->translations);

        //if has tr and tr is not empty
        if (isset($translations->tr) && !empty($translations->tr)) {
            $name = $translations->tr;
        }
        if ($autoConvert) {
            $locale = app()->getLocale();
            if (isset($translations->$locale) && !empty($translations->$locale)) {
                $name = $translations->$locale;
            }
        }

        return $name;
    }
}
