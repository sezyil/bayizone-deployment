<?php

namespace App\Http\Requests\Store;

use App\Enums\VariantTypesEnum;
use App\Models\Customer;
use App\Models\System\Currency;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //get uri parameter
        $customer_id = $this->route("customer_id");
        //check if the customer_id is valid
        if (Customer::where("id", $customer_id)->exists()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $customer_id = $this->route("customer_id");
        $customer = Customer::find($customer_id);
        $products = $customer->products->pluck("uuid")->toArray();
        return [
            "customer" => "required|array",
            "customer.authorized_name" => "required|string",
            //company_name if customer type is corporate
            "customer.company_name" => [
                Rule::when($this->input("customer.type") == "corporate", ["required", "string"]),
            ],
            "customer.address" => "nullable|string",
            //phone
            "customer.phone" => "required|numeric",
            "customer.country_id" => [
                new CountryRule(),
            ],
            "customer.state_id" => [
                new StateRule($this->input("customer.country_id")),
            ],
            "customer.city_id" => [
                new CityRule($this->input("customer.country_id"), $this->input("customer.state_id")),
            ],
            "customer.email" => "required|string",
            "customer.note" => "nullable|string",
            //individual,corporate
            "customer.type" => ["required", "string", "in:individual,corporate"],
            "customer.currency" => "required|string|in:" . Currency::all()->pluck("code")->implode(","),
            "cart" => "required|array",
            "cart.*.uuid" => [
                "required",
                "string",
                Rule::in($products),
            ],
            "cart.*.quantity" => "required|numeric",
            "cart.*.color" => "nullable|array",
            "cart.*.color.*.product_variant_id" => "required_with:lines.*.color|string",
            "cart.*.color.*.product_variant_value_id" => "required_with:lines.*.color|string",
            "cart.*.color.*.variant_id" => "required_with:lines.*.color|string",
            "cart.*.dimension" => "nullable|array",
            "cart.*.dimension.*.product_variant_id" => "required_with:lines.*.dimension|string",
            "cart.*.dimension.*.product_variant_value_id" => "required_with:lines.*.dimension|string",
            "cart.*.dimension.*.variant_id" => "required_with:lines.*.dimension|string",
        ];
    }
}
