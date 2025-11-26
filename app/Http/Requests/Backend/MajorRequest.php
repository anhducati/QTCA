<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class MajorRequest extends FormRequest
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
       

        $rules = [
            'name' => 'required|max:255|unique:majors',
            'name_en' => 'required|max:255|unique:majors',
            'subject_compo' => 'required',
            'major_code' => 'required|max:255|unique:majors',
            'image_file' => 'required|max:255',
            'contents'=> 'required',
        ];
        

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống',
            'name_en.required' => ':attribute không được để trống',
            'subject_compo.required' => ':attribute không được để trống',
            'major_code.required' => ':attribute không được để trống',
            'contents.required' => ':attribute không được để trống',
            'image_file.required' => ':attribute không được để trống',
            

            'name.max' => ':attribute tối đa 255 ký tự',
            'name_en.max' => ':attribute tối đa 255 ký tự',
            'major_code.max' => ':attribute tối đa 255 ký tự',
            'image_file.max' => ':attribute tối đa 255 ký tự',

            'name.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'name_en.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'major_code.unique' => ':attribute đã tồn tại',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên ngành',
            'name_en' => 'Tên ngành En',
            'subject_compo' => 'Tổ hợp xét tuyển',
            'major_code' => 'Mã ngành',
            'image_file' => 'Hình ảnh',
            'contents'=> 'Nội dung',
        ];
    }
}
