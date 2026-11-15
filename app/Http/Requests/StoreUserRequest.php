<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'status'   => ['required', 'in:active,inactive'],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['string', 'exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'الاسم مطلوب',
            'email.required'     => 'البريد مطلوب',
            'email.unique'       => 'البريد مستخدم مسبقاً',
            'password.required'  => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'status.required'    => 'الحالة مطلوبة',
        ];
    }
}
