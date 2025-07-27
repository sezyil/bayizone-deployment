<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultiLanguageRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //get key
        $key = explode('.', $attribute)[1];
        //if key is tr
        if ($key == 'tr') {
            //if empty string
            if (empty($value)) {
                $fail('Türkçe isim Girişi Zorunludur.');
            }
        } elseif ($key == 'en') {
            //if empty string
            if (empty($value)) {
                $fail('İngilizce isim Girişi Zorunludur.');
            }
        } else {
            $fail('Geçersiz Dil');
        }
    }
}
