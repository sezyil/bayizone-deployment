<?php

namespace App\Http\Requests\Client;

use App\Enums\VariantTypesEnum;
use App\Libraries\Client\SanctumHelper;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerOfferRequest extends FormRequest
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
        return [
            "detail" => "required|array",
            "detail.company_customer_id" => [
                "required",
                "string",
                Rule::exists("company_customers", "id")->where(function ($query) use ($customer_id) {
                    $query->where("customer_id", $customer_id);
                }),
            ],
            /* offer_date */
            "detail.offer_date" => "required|date",
            /* offer_due_date */
            "detail.offer_due_date" => "required|date",
            //incoterms
            /* "detail.incoterms" => "nullable|string", */
            /* currency */
            "detail.currency" => [
                "required",
                "string",
                "exists:currencies,code",
            ],
            /* note */
            "detail.note" => "nullable|string",
            /* billing_address */
            "detail.billing_address" => "string",
            /* billing_city_id */
            "detail.billing_country_id" => [
                new CountryRule,
            ],
            "detail.billing_city_id" => [
                new CityRule($this->detail["billing_country_id"], $this->detail["billing_state_id"]),
            ],
            /* billing_country_id */
            /* billing_state_id */
            "detail.billing_state_id" => [
                new StateRule($this->detail["billing_country_id"]),
            ],
            /* billing_zip_code */
            "detail.billing_zip_code" => "nullable|string",
            /* is_international */
            "detail.is_international" => "required|boolean",
            /* payment_account_name */
            "detail.payment_account_name" => "required|string",
            /* payment_account_number */
            "detail.payment_account_number" => "required|string",
            /* payment_bank_name */
            "detail.payment_bank_name" => "required|string",
            /* payment_branch_name */
            "detail.payment_branch_name" => "required|string",
            /* payment_iban */
            "detail.payment_iban" => "required|string",
            /* payment_swift_code */
            "detail.payment_swift_code" => "nullable|string",
            /* delivery_type */
            "detail.delivery_type" => "nullable|string",
            /* payment_type */
            "detail.payment_type" => "nullable|string",
            /* lines */
            "lines" => "required|array|min:1",
            /* product_id */
            "lines.*.product_id" => [
                "required",
                "integer",
                Rule::exists("products", "id")->where(function ($query) use ($customer_id) {
                    $query->where("customer_id", $customer_id);
                }),
            ],
            /* quantity */
            "lines.*.quantity" => [
                "required",
                "numeric",
                "min:1",
            ],
            /* unit_price */
            "lines.*.unit_price" => [
                "required",
                "numeric",
                "min:1",
            ],
            /* tax_rate */
            "lines.*.tax_rate" => [
                "required",
                "numeric",
                "min:0",
                "max:100",
            ],
            /* unit_discount_rate */
            "lines.*.unit_discount_rate" => [
                "required",
                "numeric",
                "min:0",
                "max:100",
            ],
            /* note */
            "lines.*.note" => "nullable|string",
            /* unit_volume */
            "lines.*.unit_volume" => "min:0|numeric",
            /* unit_package */
            "lines.*.unit_package" => "min:0|numeric",
            /* {
                "color": {
                    "id": "9c06ae44-d5ea-486b-bc77-761edfeb5687",
                    "value": {
                        "en": "Yellow",
                        "tr": "Sarı"
                    }
                }
            } */
            "lines.*.color" => "nullable|array",
            "lines.*.color.*.product_variant_id" => "required_with:lines.*.color|string",
            "lines.*.color.*.value_id" => "required_with:lines.*.color|string",
            "lines.*.color.*.variant_id" => "required_with:lines.*.color|string",
            /* {
                "dimension": {
                    "id": "9c06ae44-d76b-4d4e-9c18-5499beaae1a1",
                    "value": {
                        "height": 3,
                        "length": 123,
                        "width": 123
                    }
                }
            } */
            "lines.*.dimension" => "nullable|array",
            "lines.*.dimension.*.product_variant_id" => "required_with:lines.*.dimension|string",
            "lines.*.dimension.*.value_id" => "required_with:lines.*.dimension|string",
            "lines.*.dimension.*.variant_id" => "required_with:lines.*.dimension|string",
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
            "detail.company_customer_id.required" => "Firma müşterisi zorunludur.",
            "detail.company_customer_id.exists" => "Firma müşterisi bulunamadı.",
            "detail.offer_date.required" => "Teklif tarihi zorunludur.",
            "detail.offer_date.date" => "Teklif tarihi geçersiz.",
            "detail.offer_due_date.required" => "Teklif geçerlilik tarihi zorunludur.",
            "detail.offer_due_date.date" => "Teklif geçerlilik tarihi geçersiz.",
            "detail.currency.required" => "Para birimi zorunludur.",
            "detail.currency.exists" => "Para birimi bulunamadı.",
            "detail.billing_country_id.required" => "Fatura ülkesi zorunludur.",
            "detail.billing_country_id.exists" => "Fatura ülkesi bulunamadı.",
            "detail.billing_city_id.required" => "Fatura şehri zorunludur.",
            "detail.billing_city_id.exists" => "Fatura şehri bulunamadı.",
            "detail.billing_state_id.required" => "Fatura ilçesi zorunludur.",
            "detail.billing_state_id.exists" => "Fatura ilçesi bulunamadı.",
            "detail.payment_account_name.required" => "Ödeme hesap adı zorunludur.",
            "detail.payment_account_number.required" => "Ödeme hesap numarası zorunludur.",
            "detail.payment_bank_name.required" => "Ödeme banka adı zorunludur.",
            "detail.payment_branch_name.required" => "Ödeme şube adı zorunludur.",
            "detail.payment_iban.required" => "Ödeme IBAN zorunludur.",
            "lines.required" => "Ürünler zorunludur.",
            "lines.min" => "En az bir ürün eklemelisiniz.",
            "lines.*.product_id.required" => "Ürün zorunludur.",
            "lines.*.product_id.exists" => "Ürün bulunamadı.",
            "lines.*.quantity.required" => "Miktar zorunludur.",
            "lines.*.quantity.numeric" => "Miktar sayısal olmalıdır.",
            "lines.*.quantity.min" => "Miktar en az 1 olmalıdır.",
            "lines.*.unit_price.required" => "Birim fiyat zorunludur.",
            "lines.*.unit_price.numeric" => "Birim fiyat sayısal olmalıdır.",
            "lines.*.unit_price.min" => "Birim fiyat en az 1 olmalıdır.",
            "lines.*.tax_rate.required" => "Vergi oranı zorunludur.",
            "lines.*.tax_rate.numeric" => "Vergi oranı sayısal olmalıdır.",
            "lines.*.tax_rate.min" => "Vergi oranı en az 0 olmalıdır.",
            "lines.*.tax_rate.max" => "Vergi oranı en fazla 100 olmalıdır.",
            "lines.*.unit_discount_rate.required" => "Birim indirim oranı zorunludur.",
            "lines.*.unit_discount_rate.numeric" => "Birim indirim oranı sayısal olmalıdır.",
            "lines.*.unit_discount_rate.min" => "Birim indirim oranı en az 0 olmalıdır.",
            "lines.*.unit_discount_rate.max" => "Birim indirim oranı en fazla 100 olmalıdır.",
            "lines.*.color.array" => "Renk bilgisi dizi tipinde olmalıdır.",
            "lines.*.color.*.product_variant_id.required_with" => "Renk bilgisi zorunludur.",
            "lines.*.color.*.product_variant_id.string" => "Renk bilgisi metin tipinde olmalıdır.",
            "lines.*.color.*.value_id.required_with" => "Renk değeri bilgisi zorunludur.",
            "lines.*.color.*.value_id.string" => "Renk değeri bilgisi metin tipinde olmalıdır.",
            "lines.*.color.*.variant_id.required_with" => "Renk varyant bilgisi zorunludur.",
            "lines.*.color.*.variant_id.string" => "Renk varyant bilgisi metin tipinde olmalıdır.",
            "lines.*.dimension.array" => "Boyut bilgisi dizi tipinde olmalıdır.",
            "lines.*.dimension.*.product_variant_id.required_with" => "Boyut bilgisi zorunludur.",
            "lines.*.dimension.*.product_variant_id.string" => "Boyut bilgisi metin tipinde olmalıdır.",
            "lines.*.dimension.*.value_id.required_with" => "Boyut değeri bilgisi zorunludur.",
            "lines.*.dimension.*.value_id.string" => "Boyut değeri bilgisi metin tipinde olmalıdır.",
            "lines.*.dimension.*.variant_id.required_with" => "Boyut varyant bilgisi zorunludur.",
            "lines.*.dimension.*.variant_id.string" => "Boyut varyant bilgisi metin tipinde olmalıdır.",
        ];
    }
}
