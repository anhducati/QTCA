<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email không để trống',
            'email.email' => 'Email không đúng định dạng. Vd: abc@gmail.com',
            'email.max' => 'Email tối đa 255 ký tự',

            'password.required' => 'Mật khẩu không để trống',
            'password.max' => 'Mật khẩu tối đa 255 ký tự',
        ];
    }
}
