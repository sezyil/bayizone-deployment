<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'fullname' => 'required|string|max:255',
            'password' => 'sometimes|nullable|min:8|string|confirmed',
            'password_confirmation' => 'sometimes|nullable|required_with:password|string|same:password|min:8',
        ];
    }
}
