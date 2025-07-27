<?php

namespace App\Http\Requests\Client\CompanyCustomer;

use App\Libraries\Client\SanctumHelper;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyCustomerWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string|email',
            'contact_person'    => 'required|string',
            'contact_person_phone'  => 'required|string',
            'contact_person_email'  => 'string|email',
            'country_id' => [
                new CountryRule,
            ],
            'state_id' => [
                new StateRule($this->country_id),
            ],
            'city_id' => [
                new CityRule($this->country_id, $this->state_id),
            ],
            'zip_code' => 'string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Adres adı alanı gereklidir.',
            'address.required' => 'Adres alanı gereklidir.',
            'phone.required' => 'Telefon alanı gereklidir.',
            'email.required' => 'E-posta alanı gereklidir.',
            'contact_person.required' => 'Yetkili kişi alanı gereklidir.',
            'contact_person_phone.required' => 'Yetkili kişi telefon alanı gereklidir.',
            'country.required' => 'Ülke alanı gereklidir.',
            'city.required' => 'Şehir alanı gereklidir.',
            'district.required' => 'İlçe alanı gereklidir.',
            'contact_person_email.email' => 'Yetkili kişi e-posta alanı geçerli bir e-posta adresi olmalıdır.',

        ];
    }
}
