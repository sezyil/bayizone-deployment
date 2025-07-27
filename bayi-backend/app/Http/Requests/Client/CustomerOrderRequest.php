<?php

namespace App\Http\Requests\Client;

use App\Enums\VariantTypesEnum;
use App\Libraries\Client\SanctumHelper;
use App\Models\System\Countries;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerOrderRequest extends FormRequest
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
            "detail.order_date" => "required|date",
            /* currency */
            "detail.currency" => [
                "required",
                "string",
                "exists:currencies,code",
            ],
            /* note */
            "detail.note" => "nullable|string",
            //incoterms
            /* "detail.incoterms" => "nullable|string", */
            /* billing_address */
            "detail.billing_address" => "string",
            /* billing_city_id */
            /* billing_country_id */
            "detail.billing_country_id" => [new CountryRule],
            /* billing_state_id */
            "detail.billing_state_id" => [
                new StateRule($this->detail["billing_country_id"]),
            ],
            "detail.billing_city_id" => [
                new CityRule($this->detail["billing_country_id"], $this->detail["billing_state_id"]),
            ],
            /* billing_zip_code */
            "detail.billing_zip_code" => "nullable|string",
            /* is_international */
            "detail.is_international" => "boolean",
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
            "lines.*.unit_volume" => "min:0|numeric",
            /* unit_package */
            "lines.*.unit_package" => "min:0|numeric",
            /* [,
                {
                "color": {
                    "id": "9c06ae44-d5ea-486b-bc77-761edfeb5687",
                    "value": {
                        "en": "Yellow",
                        "tr": "Sarı"
                    }
                }
            }, */
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
     */
    public function messages(): array
    {
        return [
            "detail.company_customer_id.required" => "Firma müşteri bilgisi zorunludur.",
            "detail.company_customer_id.exists" => "Firma müşteri bilgisi geçersiz.",
            "detail.order_date.required" => "Sipariş tarihi zorunludur.",
            "detail.order_date.date" => "Sipariş tarihi geçerli bir tarih olmalıdır.",
            "detail.currency.required" => "Para birimi zorunludur.",
            "detail.currency.exists" => "Para birimi geçersiz.",
            "detail.note.string" => "Not alanı metin tipinde olmalıdır.",
            "detail.billing_address.string" => "Fatura adresi metin tipinde olmalıdır.",
            "detail.billing_country_id.required" => "Fatura ülke bilgisi zorunludur.",
            "detail.billing_state_id.required" => "Fatura il bilgisi zorunludur.",
            "detail.billing_city_id.required" => "Fatura ilçe bilgisi zorunludur.",
            "detail.billing_zip_code.string" => "Fatura posta kodu metin tipinde olmalıdır.",
            "detail.is_international.boolean" => "Uluslararası sipariş bilgisi boolean tipinde olmalıdır.",
            "detail.delivery_type.string" => "Teslimat tipi metin tipinde olmalıdır.",
            "detail.payment_type.string" => "Ödeme tipi metin tipinde olmalıdır.",
            "lines.required" => "Ürün bilgisi zorunludur.",
            "lines.array" => "Ürün bilgisi dizi tipinde olmalıdır.",
            "lines.min" => "En az bir ürün bilgisi girilmelidir.",
            "lines.*.product_id.required" => "Ürün bilgisi zorunludur.",
            "lines.*.product_id.integer" => "Ürün bilgisi tam sayı olmalıdır.",
            "lines.*.product_id.exists" => "Ürün bilgisi geçersiz",
            "lines.*.quantity.required" => "Miktar bilgisi zorunludur.",
            "lines.*.quantity.numeric" => "Miktar bilgisi sayısal olmalıdır.",
            "lines.*.quantity.min" => "Miktar bilgisi en az 1 olmalıdır.",
            "lines.*.unit_price.required" => "Birim fiyat bilgisi zorunludur.",
            "lines.*.unit_price.numeric" => "Birim fiyat bilgisi sayısal olmalıdır.",
            "lines.*.unit_price.min" => "Birim fiyat bilgisi en az 1 olmalıdır.",
            "lines.*.tax_rate.required" => "Vergi oranı bilgisi zorunludur.",
            "lines.*.tax_rate.numeric" => "Vergi oranı bilgisi sayısal olmalıdır.",
            "lines.*.tax_rate.min" => "Vergi oranı bilgisi en az 0 olmalıdır.",
            "lines.*.tax_rate.max" => "Vergi oranı bilgisi en fazla 100 olmalıdır.",
            "lines.*.unit_discount_rate.required" => "Birim indirim oranı bilgisi zorunludur.",
            "lines.*.unit_discount_rate.numeric" => "Birim indirim oranı bilgisi sayısal olmalıdır.",
            "lines.*.unit_discount_rate.min" => "Birim indirim oranı bilgisi en az 0 olmalıdır.",
            "lines.*.unit_discount_rate.max" => "Birim indirim oranı bilgisi en fazla 100 olmalıdır.",
            "lines.*.note.string" => "Not alanı metin tipinde olmalıdır.",
            "lines.*.unit_volume.min" => "Birim hacim bilgisi en az 0 olmalıdır.",
            "lines.*.unit_volume.numeric" => "Birim hacim bilgisi sayısal olmalıdır.",
            "lines.*.unit_package.min" => "Birim paket bilgisi en az 0 olmalıdır.",
            "lines.*.unit_package.numeric" => "Birim paket bilgisi sayısal olmalıdır.",
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
