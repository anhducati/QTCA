<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class MajorUpdateRequest extends FormRequest
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
        $majorId = $this->route('id');
        $rules = [
            'name' => 'required|max:255|unique:majors',
            'name_en' => 'required|max:255|unique:majors',
            'subject_compo' => 'required',
            'major_code' => 'required|max:255',
            'image_file' => 'max:255|mimes:jpeg,jpg,png,gif|max:2048',
            'slug' => 'required|alpha_dash',
            'contents'=> 'required',
        ];
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'required|max:255|unique:majors,name,' . $majorId;
            $rules['name_en'] = 'required|max:255|unique:majors,name_en,' . $majorId;
        //     // $rules['subject_compo'] = 'required'.$majorId;
        //     $rules['major_code'] = 'required|max:255|unique:majors,major_code,' . $majorId;
        //     $rules['slug'] = 'required|max:255|unique:majors,slug,' . $majorId;
        }
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

            'name.max' => ':attribute tối đa 255 ký tự',
            'name_en.max' => ':attribute tối đa 255 ký tự',
            'major_code.max' => ':attribute tối đa 255 ký tự',
            'image_file.max' => ':attribute tối đa 255 ký tự',
            'name.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'name_en.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',

            'slug.alpha_dash' => ':attribute sai định dạng, ví dụ: duong-dan-hi-hi',

            'name.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'name_en.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',

            // Thêm thông báo lỗi cho trường image_file
            'image_file.mimes' => 'Định dạng ảnh không hợp lệ. Hãy chọn ảnh có định dạng jpeg, jpg, png hoặc gif.',
            'image_file.max' => 'Kích thước ảnh quá lớn. Hãy chọn ảnh có kích thước nhỏ hơn 2MB.',
            'name.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
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
            'slug' => 'Đường dẫn',
            'contents'=> 'Nội dung',
        ];
    }
}
