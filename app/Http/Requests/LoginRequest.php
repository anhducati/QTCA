<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' =>  'required|email|max:255',
            'password' => 'required|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => ':attribute không được để trống',
            'email.required' => ':attribute không được để trống',
            'email.email' => ':attribute không đúng định dạng',

            'password.max' => ':attribute tối đa 255 ký tự',
            'email.max' => ':attribute tối đa 255 ký tự',

        ];


    }

    public function attributes(): array
    {
        return [
            'name' => 'Họ tên',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'password' => 'Mật khẩu',
            'confirm_password' => 'Xác nhận mật khẩu',
        ];
    }
}
