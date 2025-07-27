<?php

namespace App\Http\Requests\Client;

use App\Libraries\Client\SanctumHelper;
use App\Models\System\Countries;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
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
        $customer_id = SanctumHelper::customer_id();
        //columns from customer table
        return [
            'firm_name' => 'required|string|max:50',
            'tax_no' => 'required|numeric|digits_between:10,11|unique:customer,tax_no,' . $customer_id . ',id',
            'tax_administration' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'country_id' => [
                'required',
                new CountryRule
            ],
            'city_id' => [
                new CityRule($this->country_id, $this->state_id)
            ],
            'state_id' => [
                new StateRule($this->country_id)
            ],
            'phone' => 'required|digits_between:0,30',
            'authorized_person' => 'required|string|max:50',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'firm_name.required' => 'Firma adı alanı boş bırakılamaz.',
            'firm_name.string' => 'Firma adı alanı metin tipinde olmalıdır.',
            'firm_name.max' => 'Firma adı alanı en fazla 255 karakter olmalıdır.',
            'tax_no.required' => 'Vergi numarası alanı boş bırakılamaz.',
            'tax_no.numeric' => 'Vergi numarası alanı sayı tipinde olmalıdır.',
            'tax_no.digits_between' => 'Vergi numarası alanı en az :min en fazla :max karakter olmalıdır.',
            'tax_no.unique' => 'Vergi numarası daha önce kullanılmış.',
            'tax_administration.required' => 'Vergi dairesi alanı boş bırakılamaz.',
            'tax_administration.string' => 'Vergi dairesi alanı metin tipinde olmalıdır.',
            'tax_administration.max' => 'Vergi dairesi alanı en fazla 255 karakter olmalıdır.',
            'address.required' => 'Adres alanı boş bırakılamaz.',
            'address.string' => 'Adres alanı metin tipinde olmalıdır.',
            'address.max' => 'Adres alanı en fazla 255 karakter olmalıdır.',
            'postcode.string' => 'Posta kodu alanı metin tipinde olmalıdır.',
            'postcode.max' => 'Posta kodu alanı en fazla 255 karakter olmalıdır.',
            'country_id.required' => 'Ülke alanı boş bırakılamaz.',
            'country_id.exists' => 'Ülke alanı geçerli bir ülke olmalıdır.',
            'state_id.required' => 'İl alanı boş bırakılamaz.',
            'state_id.exists' => 'İl alanı geçerli bir il olmalıdır.',
            'city_id.required' => 'İlçe alanı boş bırakılamaz.',
            'city_id.exists' => 'İlçe alanı geçerli bir ilçe olmalıdır.',
            'phone.required' => 'Telefon alanı boş bırakılamaz.',
            'phone.digits_between' => 'Telefon alanı en az :min en fazla :max karakter olmalıdır.',
            'fax.required' => 'Fax alanı boş bırakılamaz.',
            'fax.string' => 'Fax alanı metin tipinde olmalıdır.',
            'fax.max' => 'Fax alanı en fazla 255 karakter olmalıdır.',
            'authorized_person.required' => 'Yetkili kişi alanı boş bırakılamaz.',
            'authorized_person.string' => 'Yetkili kişi alanı metin tipinde olmalıdır.',
            'authorized_person.max' => 'Yetkili kişi alanı en fazla 255 karakter olmalıdır.',
        ];
    }
}
