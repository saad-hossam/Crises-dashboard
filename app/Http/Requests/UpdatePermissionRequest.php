<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'group' => is_string($this->group) ? trim($this->group) : $this->group,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($this->route('permission')),
                'regex:/^[a-z0-9_]+(\.[a-z0-9_]+)?$/',
            ],
            'group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('permissions.validation.name_required'),
            'name.unique'   => __('permissions.validation.name_unique'),
            'name.regex'    => __('permissions.validation.name_regex'),
            'group.required' => __('permissions.validation.group_required'),
        ];
    }
}