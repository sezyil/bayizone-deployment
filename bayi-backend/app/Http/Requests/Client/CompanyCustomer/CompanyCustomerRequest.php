<?php

namespace App\Http\Requests\Client\CompanyCustomer;

use App\Enums\CompanyCustomerGroupEnum;
use App\Libraries\Client\SanctumHelper;
use App\Models\System\Language;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyCustomerRequest extends FormRequest
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
        $type = $this->input('type');
        $customer_id = SanctumHelper::customer_id();

        $companyId = $this->route('company_customer');

        $tax_uniqRule = Rule::unique('company_customers', 'tax_identity_no')->where(function ($query) use ($customer_id) {
            $query->where('customer_id', $customer_id);
        });

        $mail_uniqRule = Rule::unique('company_customers', 'email')->where(function ($query) use ($customer_id) {
            $query->where('customer_id', $customer_id);
        });

        if ($companyId) {
            $tax_uniqRule->ignore($companyId);
            $mail_uniqRule->ignore($companyId);
        }



        return [
            'authorized_name' => 'required|string|max:30',
            'tax_identity_no' => [
                'nullable',
                'digits_between:10,11',
                $tax_uniqRule,
            ],
            'company_name' => 'required|string|max:60',
            //if type is 1, tax_office is required
            'tax_office' => 'nullable|string|max:30',
            'phone' => 'required|digits_between:0,30',
            'fax' => 'nullable|string|max:20',
            'language' => 'required|string|in:' . Language::all()->pluck('code')->implode(','),
            'email' => [
                'required',
                'email',
                'max:100',
                $mail_uniqRule,
            ],
            'address' => 'required|string|max:255',
            'country_id' => [new CountryRule],
            'state_id' => [
                new StateRule($this->country_id),
            ],
            'city_id' => [
                new CityRule($this->country_id, $this->state_id),
            ],
            'postcode' => 'nullable|string|max:10',
            'type' => [
                'required',
                'integer',
                'in:1,2',
            ],
            'status' => 'required|boolean',
            'group' => 'required|string|in:' . implode(',', CompanyCustomerGroupEnum::getValues()),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        $tax_label = $this->input('type') === 1 ? 'Vergi/Vat no' : 'TC kimlik no';
        return [
            'authorized_name.required' => 'Yetkili adı alanı zorunludur.',
            'authorized_name.string' => 'Yetkili adı alanı metin tipinde olmalıdır.',
            'authorized_name.max' => 'Yetkili adı alanı en fazla 30 karakter olmalıdır.',
            'tax_identity_no.required' => $tax_label . ' alanı zorunludur.',
            'tax_identity_no.numeric' => $tax_label . ' alanı sayısal olmalıdır.',
            'tax_identity_no.string' => $tax_label . ' alanı metin tipinde olmalıdır.',
            'tax_identity_no.max' => $tax_label . ' alanı en fazla 11 karakter olmalıdır.',
            'tax_office.required' => 'Vergi dairesi alanı zorunludur.',
            'tax_office.string' => 'Vergi dairesi alanı metin tipinde olmalıdır.',
            'tax_office.max' => 'Vergi dairesi alanı en fazla 30 karakter olmalıdır.',
            'company_name.required' => 'Firma adı alanı zorunludur.',
            'company_name.string' => 'Firma adı alanı metin tipinde olmalıdır.',
            'company_name.max' => 'Firma adı alanı en fazla 60 karakter olmalıdır.',
            'phone.required' => 'Telefon alanı zorunludur.',
            'phone.digits_between' => 'Telefon alanı en az 0 en fazla 30 karakter olmalıdır.',
            'fax.required' => 'Fax alanı zorunludur.',
            'fax.string' => 'Fax alanı metin tipinde olmalıdır.',
            'fax.max' => 'Fax alanı en fazla 20 karakter olmalıdır.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'E-posta alanı e-posta tipinde olmalıdır.',
            'email.max' => 'E-posta alanı en fazla 100 karakter olmalıdır.',
            'address.required' => 'Adres alanı zorunludur.',
            'address.string' => 'Adres alanı metin tipinde olmalıdır.',
            'address.max' => 'Adres alanı en fazla 255 karakter olmalıdır.',
            'country_id.required' => 'Ülke alanı zorunludur.',
            'country_id.integer' => 'Ülke alanı tamsayı tipinde olmalıdır.',
            'country_id.exists' => 'Ülke alanı geçerli bir ülke olmalıdır.',
            'state_id.required' => 'İl alanı zorunludur.',
            'state_id.integer' => 'İl alanı tamsayı tipinde olmalıdır.',
            'state_id.exists' => 'İl alanı geçerli bir il olmalıdır.',
            'city_id.required' => 'İlçe alanı zorunludur.',
            'city_id.integer' => 'İlçe alanı tamsayı tipinde olmalıdır.',
            'city_id.exists' => 'İlçe alanı geçerli bir ilçe olmalıdır.',
            'district_id.required' => 'İlçe alanı zorunludur.',
            'district_id.integer' => 'İlçe alanı tamsayı tipinde olmalıdır.',
            'district_id.exists' => 'İlçe alanı geçerli bir ilçe olmalıdır.',
            'postcode.string' => 'Posta kodu alanı metin tipinde olmalıdır.',
            'postcode.max' => 'Posta kodu alanı en fazla 10 karakter olmalıdır.',
            'country.required' => 'Ülke alanı zorunludur.',
            'country.string' => 'Ülke alanı metin tipinde olmalıdır.',
            'country.max' => 'Ülke alanı en fazla 20 karakter olmalıdır.',
            'type.required' => 'Tip alanı zorunludur.',
            'type.integer' => 'Tip alanı tamsayı tipinde olmalıdır.',
            'type.in' => 'Müşteri tipini seçiniz',
            'status.required' => 'Durum alanı zorunludur.',
            'status.boolean' => 'Durum alanı mantıksal tipinde olmalıdır.',
        ];
    }
}
