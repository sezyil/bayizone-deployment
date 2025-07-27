<?php

namespace App\Http\Requests\Client;

use App\Libraries\Client\SanctumHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountRequest extends FormRequest
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
        $editMode = $this->route()->hasParameter('bank_account');
        $customer_id = SanctumHelper::customer_id();
        $id = $this->route()->parameter('bank_account');
        $ibanRule = Rule::unique('customer_bank_accounts')->where(function ($query) use ($customer_id) {
            return $query->where('customer_id', $customer_id);
        });

        $swiftCodeRule = Rule::unique('customer_bank_accounts')->where(function ($query) use ($customer_id) {
            return $query->where('customer_id', $customer_id);
        });

        if ($editMode) {
            $ibanRule = $ibanRule->ignore($id);
            $swiftCodeRule = $swiftCodeRule->ignore($id);
        }

        return [
            'bank_name' => 'required|string',
            'branch_name' => 'required|string',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'iban' => [
                'required',
                $ibanRule
            ],
            'swift_code' => [
                'nullable',
                $swiftCodeRule
            ],
            'currency' => [
                'required',
                Rule::exists('currencies', 'code')->where(function ($query) {
                    return $query->where('status', 1);
                })
            ],
            'status' => 'required|boolean',
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
            'bank_name.required' => 'Banka adı zorunludur.',
            'branch_name.required' => 'Şube adı zorunludur.',
            'account_name.required' => 'Hesap adı zorunludur.',
            'account_number.required' => 'Hesap numarası zorunludur.',
            'iban.required' => 'IBAN zorunludur.',
            'iban.unique' => 'IBAN daha önce kayıt edilmiş.',
            'swift_code.unique' => 'SWIFT kodu daha önce kayıt edilmiş.',
            'swift_code.required' => 'SWIFT kodu zorunludur.',
            'currency.required' => 'Para birimi zorunludur.',
            'currency.exists' => 'Para birimi bulunamadı.',
            'status.required' => 'Durum zorunludur.',
            'status.boolean' => 'Durum alanı mantıksal bir değer olmalıdır.',
        ];
    }
}
