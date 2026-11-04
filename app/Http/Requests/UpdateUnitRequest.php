<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('units', 'name')->ignore($this->route('unit')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('units.validation.name_required'),
            'name.unique'   => __('units.validation.name_unique'),
            'name.max'      => __('units.validation.name_max'),
        ];
    }
}