<?php

namespace App\Rules;

use App\Models\System\Countries;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StateRule implements ValidationRule
{
    private $country_id;
    public function __construct($country_id)
    {
        $this->country_id = $country_id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validate the state.
        $state = Countries::find($this->country_id)->states ?? collect();
        if ($state->count() > 0) {
            $hasState = $state->contains('id', $value);
            if (!$hasState) {
                $fail('İl alanı geçerli bir il olmalıdır.');
                return;
            }
        }
    }
}
