<?php

namespace App\Rules;

use App\Models\System\Countries;
use App\Models\System\States;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CityRule implements ValidationRule
{
    private $country_id;
    private $state_id;
    public function __construct($country_id, $state_id)
    {
        $this->country_id = $country_id;
        $this->state_id = $state_id;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validate the city.
        //first if has state else not required to control city
        if ($this->state_id) {
            $state = Countries::find($this->country_id)?->states()?->find($this->state_id) ?? collect();
            if ($state->count() > 0) {
                //check has available city
                $hasCity = $state->cities->count() > 0;
                if ($hasCity) {
                    $hasCity = $state->cities->contains('id', $value);
                    if (!$hasCity) {
                        $fail('İlçe alanı geçerli bir ilçe olmalıdır.');
                        return;
                    }
                }
            }
        }
    }
}
