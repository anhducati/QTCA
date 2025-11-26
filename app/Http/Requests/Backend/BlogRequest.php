<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
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
//        $blogId = $this->route('id');

        $rules = [
            'title' => 'required|max:255|unique:blogs',
            'title_en' => 'required|max:255|unique:blogs',
            'slug' => 'required|alpha_dash|unique:blogs',
            'contents' => 'required',
            'contents_en' => 'required',
            'image_file' => 'required|mimes:jpeg,jpg,png,gif|max:2048',
            'meta_description' => 'required',
            'tags' => 'required',
            'meta_keyword' => 'required|max:255',
        ];

//        if ($this->isMethod('put') || $this->isMethod('patch')) {
//            $rules['title'] = 'required|max:255|unique:blogs,title,' . $blogId;
//            $rules['slug'] = 'required|max:255|unique:blogs,slug,' . $blogId;
//        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => ':attribute không được để trống',
            'title_en.required' => ':attribute không được để trống',
            'image_file.required' => 'Vui lòng chọn ảnh đại diện bài viết',
            'slug.required' => ':attribute không được để trống',
            'tags.required' => ':attribute không được để trống',
            'contents.required' => ':attribute không được để trống',
            'contents_en.required' => ':attribute không được để trống',
            'title.max' => ':attribute tối đa 255 ký tự',
            'title_en.max' => ':attribute tối đa 255 ký tự',
            'meta_title.required' => ':attribute không được để trống',
            'meta_title.max' => ':attribute tối đa 255 ký tự',
            'meta_description.required' => ':attribute không được để trống',
            'meta_keyword.required' => ':attribute không được để trống',
            'meta_keyword.max' => ':attribute tối đa 255 ký tự',
            'status.required' => ':attribute không được để trống',
            'slug.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'slug.alpha_dash' => ':attribute phải là chuỗi chữ, số, dấu gạch ngang, vd: duong-dan-hi-hi',

            // Thêm thông báo lỗi cho trường image_file
            'image_file.mimes' => 'Định dạng ảnh không hợp lệ. Hãy chọn ảnh có định dạng jpeg, jpg, png hoặc gif.',
            'image_file.max' => 'Kích thước ảnh quá lớn. Hãy chọn ảnh có kích thước nhỏ hơn 2MB.',
        ];
    }


    public function attributes(): array
    {
        return [
            'slug' => 'Đường dẫn',
            'title' => 'Tên bài viết',
            'title_en' => 'Tên bài viết en',
            'image_file' => 'Ảnh',
            'contents' => 'Nội dung',
            'contents_en' => 'Nội dung en',
            'meta_title' => 'Tiêu đề meta',
            'meta_description' => 'Miêu tả meta',
            'meta_keyword' => 'Từ khóa meta',
            'status' => 'Trạng thái',
        ];
    }
}
