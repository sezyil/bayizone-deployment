<?php

namespace App\Http\Requests\Client;

use App\Enums\CompanyCustomerGroupEnum;
use App\Enums\VariantTypesEnum;
use App\Libraries\Client\SanctumHelper;
use App\Models\Catalog\Attribute\Attributes;
use App\Models\Catalog\Category\Categories;
use App\Models\Catalog\Product\Product;
use Illuminate\Database\Query\Builder;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\System\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreProductRequest extends FormRequest
{
    public $product_id = null;
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
        $customer_products = Product::where('customer_id', $customer_id)->get();
        $customerCategories = Categories::where('customer_id', $customer_id)->orWhere('customer_id', null)->get();
        $customer_attributes = Attributes::where('customer_id', $customer_id);
        $productPlucked = $customer_products->pluck('id');
        $productFilteredIds = $productPlucked->filter(function ($value) {
            return $value != $this->product_id;
        })->toArray();
        $modelRule = [
            'required',
            'string',
            'min:' . Product::MODEL_MIN_LENGTH,
            'max:' . Product::MODEL_MAX_LENGTH,
            Rule::unique(Product::class, 'model')->where(function ($query) use ($productPlucked) {
                return $query->whereIn('id', $productPlucked->toArray());
            })
        ];

        if ($this->product_id) {
            $product_id = (int)$this->product_id;
            $modelRule = [
                'required',
                'string',
                'min:' . Product::MODEL_MIN_LENGTH,
                'max:' . Product::MODEL_MAX_LENGTH,
                Rule::unique(Product::class, 'model')->where(function ($query) use ($productFilteredIds) {
                    return $query->whereIn('id', $productFilteredIds);
                })
            ];
        }

        return [
            'model' => $modelRule,
            'default_currency' => 'required|string|in:' . Currency::all()->pluck('code')->implode(','),
            'price_tl' => 'required|min:0|numeric',
            'price_usd' => 'required|min:0|numeric',
            'price_euro' => 'required|min:0|numeric',
            'price_gbp' => 'required|min:0|numeric',
            'quantity' => 'required|numeric|min:0|max:999',
            'minimum' => ['required', 'numeric', 'min:1'],
            'unit_id' => 'required|integer|exists:units,id',
            'package' => 'nullable|numeric|min:1|max:999',
            'volume' => 'nullable|numeric|min:0|max:999',
            'status' => 'required|boolean',
            'store_visibility' => 'required|boolean',
            'weight' => 'numeric|min:0|max:999',
            'width' => 'integer|min:0|max:999',
            'height' => 'integer|min:0|max:999',
            'length' => 'integer|min:0|max:999',
            //active filled or empty array
            'active_customer_group' => 'array|nullable',
            'active_customer_group.*' => 'string|in:' . implode(',', CompanyCustomerGroupEnum::getValues()),
            'descriptions' => [
                'array',
            ],
            'names' => [
                'array',
            ],
            'names.*' => [
                function ($attribute, $value, $fail) {
                    //get key
                    $key = explode('.', $attribute)[1];
                    //if key is tr
                    if ($key == 'tr') {
                        //if empty string
                        if (empty($value)) {
                            $fail('Türkçe isim Girişi Zorunludur.');
                        }
                    } elseif ($key == 'en') {
                        //if empty string
                        if (empty($value)) {
                            $fail('İngilizce isim Girişi Zorunludur.');
                        }
                    } else {
                        $fail('Geçersiz Dil');
                    }
                },
                /* custom min */
                function ($attribute, $value, $fail) {
                    //get key
                    $key = explode('.', $attribute)[1];
                    //if key is tr
                    if ($key == 'tr') {
                        //if empty string
                        if (strlen($value) < Product::NAME_MIN_LENGTH) {
                            $fail('Türkçe isim en az ' . Product::NAME_MIN_LENGTH . ' karakter olmalıdır.');
                        }
                    } elseif ($key == 'en') {
                        //if empty string
                        if (strlen($value) < Product::NAME_MIN_LENGTH) {
                            $fail('İngilizce isim en az ' . Product::NAME_MIN_LENGTH . ' karakter olmalıdır.');
                        }
                    } else {
                        $fail('Geçersiz Dil');
                    }
                },
                /* custom max */
                function ($attribute, $value, $fail) {
                    //get key
                    $key = explode('.', $attribute)[1];
                    //if key is tr
                    if ($key == 'tr') {
                        //if empty string
                        if (strlen($value) > Product::NAME_MAX_LENGTH) {
                            $fail('Türkçe isim en fazla ' . Product::NAME_MAX_LENGTH . ' karakter olmalıdır.');
                        }
                    } elseif ($key == 'en') {
                        //if empty string
                        if (strlen($value) > Product::NAME_MAX_LENGTH) {
                            $fail('İngilizce isim en fazla ' . Product::NAME_MAX_LENGTH . ' karakter olmalıdır.');
                        }
                    } else {
                        $fail('Geçersiz Dil');
                    }
                },
            ],
            'attributes' => 'array',
            'attributes.*.attribute_id' => ['distinct', 'integer', Rule::in($customer_attributes->pluck('id')->toArray())],
            'attributes.*.text' => 'required|string',
            'links.categories' => 'array',
            'links.categories.*.category_id' => ['required', 'distinct', Rule::in($customerCategories->pluck('id')->toArray())],
            'images' => 'array|min:1|max:10',
            'images.*.id' => 'required|integer',
            'images.*.image' => 'required|string|max:255',
            'images.*.sort_order' => 'required|integer',
            'colors' => 'array',
            'colors.*' => 'array',
            'colors.*.variant_id' => 'required|string',
            'colors.*.variant_value_id' => 'required|array',
            'colors.*.variant_value_id.*' => 'required|string',
            'dimensions' => 'array',
            'dimensions.*' => 'array',
            'dimensions.*.variant_id' => 'required|string',
            'dimensions.*.value' => 'required|array',
            'dimensions.*.value.*' => 'required|array|required_array_keys:width,height,length',
            'dimensions.*.value.*.width' => 'required|integer',
            'dimensions.*.value.*.height' => 'required|integer',
            'dimensions.*.value.*.length' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        /* tr */
        return [
            'model.required' => 'Model alanı zorunludur.',
            'model.string' => 'Model alanı metin olmalıdır.',
            'model.min' => 'Model alanı en az :min karakter olmalıdır.',
            'model.max' => 'Model alanı en fazla :max karakter olmalıdır.',
            'model.unique' => 'Model alanı benzersiz olmalıdır.',
            'price_tl.required' => 'TL Fiyat alanı zorunludur.',
            'price_tl.min' => 'TL Fiyat alanı en az 0 olmalıdır.',
            'price_tl.numeric' => 'TL Fiyat alanı sayı olmalıdır.',
            'price_usd.required' => 'USD Fiyat alanı zorunludur.',
            'price_usd.min' => 'USD Fiyat alanı en az 0 olmalıdır.',
            'price_usd.numeric' => 'USD Fiyat alanı sayı olmalıdır.',
            'price_euro.required' => 'EURO Fiyat alanı zorunludur.',
            'price_euro.min' => 'EURO Fiyat alanı en az 0 olmalıdır.',
            'price_euro.numeric' => 'EURO Fiyat alanı sayı olmalıdır.',
            'price_gbp.required' => 'GBP Fiyat alanı zorunludur.',
            'price_gbp.min' => 'GBP Fiyat alanı en az 0 olmalıdır.',
            'price_gbp.numeric' => 'GBP Fiyat alanı sayı olmalıdır.',
            'default_currency.required' => 'Varsayılan Para Birimi alanı zorunludur.',
            'default_currency.string' => 'Varsayılan Para Birimi alanı metin olmalıdır.',
            'default_currency.in' => 'Varsayılan Para Birimi alanı geçersiz.',
            'quantity.required' => 'Miktar alanı zorunludur.',
            'quantity.numeric' => 'Miktar alanı sayı olmalıdır.',
            'quantity.min' => 'Miktar alanı en az 0 olmalıdır.',
            'quantity.max' => 'Miktar alanı en fazla 999 olmalıdır.',
            'minimum.required' => 'Minimum miktar alanı zorunludur.',
            'minimum.numeric' => 'Minimum miktar alanı sayı olmalıdır.',
            'minimum.min' => 'Minimum miktar alanı en az 1 olmalıdır.',
            'unit_id.required' => 'Birim alanı zorunludur.',
            'unit_id.integer' => 'Birim alanı sayı olmalıdır.',
            'unit_id.exists' => 'Birim alanı geçersiz.',
            'package.numeric' => 'Paket alanı sayı olmalıdır.',
            'package.min' => 'Paket alanı en az 1 olmalıdır.',
            'package.max' => 'Paket alanı en fazla 999 olmalıdır.',
            'volume.numeric' => 'Hacim alanı sayı olmalıdır.',
            'volume.min' => 'Hacim alanı en az 0 olmalıdır.',
            'volume.max' => 'Hacim alanı en fazla 999 olmalıdır.',
            'store_visibility.required' => 'Mağaza Görünürlüğü alanı zorunludur.',
            'store_visibility.boolean' => 'Mağaza Görünürlüğü alanı doğru yada yanlış olmalıdır.',
            'weight.numeric' => 'Ağırlık alanı sayı olmalıdır.',
            'weight.min' => 'Ağırlık alanı en az 0 olmalıdır.',
            'weight.max' => 'Ağırlık alanı en fazla 999 olmalıdır.',
            'width.integer' => 'Genişlik alanı tam sayı olmalıdır.',
            'width.min' => 'Genişlik alanı en az 0 olmalıdır.',
            'width.max' => 'Genişlik alanı en fazla 999 olmalıdır.',
            'height.integer' => 'Yükseklik alanı tam sayı olmalıdır.',
            'height.min' => 'Yükseklik alanı en az 0 olmalıdır.',
            'height.max' => 'Yükseklik alanı en fazla 999 olmalıdır.',
            'length.integer' => 'Uzunluk alanı tam sayı olmalıdır.',
            'length.min' => 'Uzunluk alanı en az 0 olmalıdır.',
            'length.max' => 'Uzunluk alanı en fazla 999 olmalıdır.',

            'status.required' => 'Durum alanı zorunludur.',
            'status.boolean' => 'Durum alanı doğru yada yanlış olmalıdır.',
            'descriptions.required' => 'Açıklamalar alanı zorunludur.',
            'descriptions.array' => 'Açıklamalar alanı dizi olmalıdır.',
            'names.array' => 'İsimler alanı dizi olmalıdır.',
            'names.*.required' => 'İsimler alanı zorunludur.',
            'names.*.string' => 'İsimler alanı metin olmalıdır.',
            'names.*.min' => 'İsimler alanı en az :min karakter olmalıdır.',
            'attributes.array' => 'Özellikler alanı dizi olmalıdır.',
            'attributes.*.attribute_id.distinct' => 'Özellikler alanı benzersiz olmalıdır.',
            'attributes.*.attribute_id.integer' => 'Özellikler alanı sayı olmalıdır.',
            'attributes.*.attribute_id.in' => 'Özellikler alanı geçersiz.',
            'attributes.*.text.required' => 'Özellikler alanı zorunludur.',
            'attributes.*.text.string' => 'Özellikler alanı metin olmalıdır.',
            'links.categories.array' => 'Kategoriler alanı dizi olmalıdır.',
            'links.categories.*.category_id.required' => 'Kategoriler alanı zorunludur.',
            'links.categories.*.category_id.distinct' => 'Kategoriler alanı benzersiz olmalıdır.',
            'links.categories.*.category_id.in' => 'Kategoriler alanı geçersiz.',
            'images.array' => 'Fotoğraflar alanı dizi olmalıdır.',
            'images.min' => 'En az 1 fotoğraf eklemelisiniz.',
            'images.max' => 'En fazla 10 fotoğraf ekleyebilirsiniz.',
            'images.*.id.required' => 'Fotoğraf id alanı zorunludur.',
            'images.*.id.integer' => 'Fotoğraf id alanı sayı olmalıdır.',
            'images.*.image.required' => 'Fotoğraf alanı zorunludur.',
            'images.*.image.string' => 'Fotoğraf alanı metin olmalıdır.',
            'images.*.image.max' => 'Fotoğraf alanı en fazla 255 karakter olmalıdır.',
            'images.*.sort_order.required' => 'Sıralama alanı zorunludur.',
            'images.*.sort_order.integer' => 'Sıralama alanı sayı olmalıdır.',
            'colors.array' => 'Renkler alanı dizi olmalıdır.',
            'colors.*.array' => 'Renkler alanı dizi olmalıdır.',
            'colors.*.en.required' => 'İngilizce renk alanı zorunludur.',
            'colors.*.en.string' => 'İngilizce renk alanı metin olmalıdır.',
            'colors.*.tr.required' => 'Türkçe renk alanı zorunludur.',
            'colors.*.tr.string' => 'Türkçe renk alanı metin olmalıdır.',
            'dimensions.array' => 'Ölçüler alanı dizi olmalıdır.',
            'dimensions.*.array' => 'Ölçüler alanı dizi olmalıdır.',
            'dimensions.*.width.required' => 'Genişlik alanı zorunludur.',
            'dimensions.*.width.numeric' => 'Genişlik alanı tam sayı olmalıdır.',
            'dimensions.*.height.required' => 'Yükseklik alanı zorunludur.',
            'dimensions.*.height.numeric' => 'Yükseklik alanı tam sayı olmalıdır.',
            'dimensions.*.length.required' => 'Uzunluk alanı zorunludur.',
            'dimensions.*.length.numeric' => 'Uzunluk alanı tam sayı olmalıdır.',
        ];
    }

    public function attributes(): array
    {
        return [
            'model' => 'Model',
            'default_currency' => 'Varsayılan Para Birimi',
            'price_tl' => 'TL Fiyat',
            'price_usd' => 'USD Fiyat',
            'price_euro' => 'EURO Fiyat',
            'price_gbp' => 'GBP Fiyat',
            'quantity' => 'Miktar',
            'minimum' => 'Minimum Miktar',
            'unit_id' => 'Birim',
            'package' => 'Paket',
            'volume' => 'Hacim',
            'status' => 'Durum',
            'store_visibility' => 'Mağaza Görünürlüğü',
            'weight' => 'Ağırlık',
            'width' => 'Genişlik',
            'height' => 'Yükseklik',
            'length' => 'Uzunluk',
            'active_customer_group' => 'Aktif Müşteri Grupları',
            'descriptions' => 'Açıklamalar',
            'names' => 'İsimler',
            'attributes' => 'Özellikler',
            'links.categories' => 'Kategoriler',
            'images' => 'Fotoğraflar',
            'colors' => 'Renkler',
            'dimensions' => 'Ölçüler',
            'colors.*.value' => 'Renk Adı',
            'colors.*.value.*' => 'Renk Adı',
            'dimensions.*.value' => 'Ölçü Değerleri',
            'dimensions.*.value.width' => 'Genişlik',
            'dimensions.*.value.height' => 'Yükseklik',
            'dimensions.*.value.length' => 'Uzunluk',
        ];
    }
}
