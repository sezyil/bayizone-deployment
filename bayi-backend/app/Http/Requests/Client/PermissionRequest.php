<?php

namespace App\Http\Requests\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionRoleEnum;
use App\Libraries\Client\SanctumHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
            'data' => 'required',
            'data.id' => [
                'required',
                'string',
                Rule::exists('roles', 'id')->where(function ($query) use ($customer_id) {
                    $query->where('team_id', $customer_id)->where('name', '!=', PermissionRoleEnum::ADMIN->value);
                }),
                'distinct',
            ],
            'data.items.*' => [
                'required',
            ],
            'data.items.*.permissions' => [
                'required',
            ],
            'data.items.*.permissions.*.id' => [
                'required',
                'string',
                Rule::exists('permissions', 'id'),
                'distinct',
            ],
            'data.items.*.permissions.*.checked' => [
                'required',
                'boolean',
            ],
            'data.items.*.permissions.*.operation' => [
                'required',
                Rule::in(PermissionOperationTypes::allValues()),
            ],
        ];
    }
}
