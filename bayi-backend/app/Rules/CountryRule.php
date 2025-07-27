<?php

namespace App\Rules;

use App\Models\System\Countries;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CountryRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validate the country.
        $country = Countries::find($value);
        if (!$country) {
            $fail('Ülke alanı geçerli bir ülke olmalıdır.');
            return;
        }
    }
}
