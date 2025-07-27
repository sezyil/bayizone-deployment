<?php

namespace App\Http\Requests\Client;

use App\Enums\VariantTypesEnum;
use App\Rules\MultiLanguageRule;
use Illuminate\Foundation\Http\FormRequest;

class VariantValueRequest extends FormRequest
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
        $method = $this->getMethod();
        if ($method === 'POST') {
            return [
                /* name.tr name.en */
                'names' => 'required|array',
                'names.*' => [
                    new MultiLanguageRule
                ],
                'is_active' => 'required|boolean',
            ];
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            return [
                'names' => 'array',
                'names.*' => [
                    new MultiLanguageRule
                ],
                'is_active' => 'boolean',
            ];
        } else {
            //error
            throw new \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException([], 'Method not allowed');
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Varyant adı zorunludur.',
            'name.array' => 'Varyant adı dizi olmalıdır.',
            'name.required_array_keys' => function ($attribute) {
                return $attribute . ' alanı en ve tr olmalıdır.';
            },
            'name.*.required' => 'Varyant adı zorunludur.',
        ];
    }
}
