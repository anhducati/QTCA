<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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

//        return [
//            'name' => 'required|max:255',
//            'email' => 'required|email|max:255|unique:users',
//            'password' => 'required|max:255',
//            'confirm-password' => 'required|max:255|same:password',
//        ];


//        Route::put('panel/user/update/{id}', '...');
        $userId = $this->route('id');

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            // 'password' => 'required|max:255',
            // 'confirm-password' => 'required|max:255|same:password',
        ];

        // If it's an update request, exclude the current user's ID from unique check
        // Trường hợp cập nhật user
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $userId;
            $rules['password'] = '';
            $rules['confirm-password'] = '';
        }

        return $rules;

    }

    public function messages(): array
    {
        return [
           
            'email.required' => 'Email không để trống',
            'email.email' => 'Email không đúng định dạng. Vd: abc@gmail.com',
            'email.max' => 'Email tối đa 255 ký tự',
            'email.unique' => 'Email đã tồn tại',

            'password.required' => 'Mật khẩu không để trống',
            'password.max' => 'Mật khẩu tối đa 255 ký tự',

            'confirm-password.required' => 'Xác nhận mật khẩu không để trống',
            'confirm-password.max' => 'Xác nhận mật khẩu tối đa 255 ký tự',
            'confirm-password.same' => 'Xác nhận mật khẩu không trùng khớp mật khẩu',
        ];
    }
}
