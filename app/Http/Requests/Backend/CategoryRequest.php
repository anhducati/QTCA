<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('id');

        $rules = [
            'name' => 'required|max:255|unique:categories',
            'name_en' => 'required|max:255|unique:categories',
            'slug' => 'required|alpha_dash|unique:categories',
            'title' => 'required|max:255',
            'meta_title' => 'required|max:255',
            'meta_description' => 'required|max:255',
            'meta_keyword' => 'required|max:255',
            'status' => 'required',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['name'] = 'required|max:255|unique:categories,name,' . $categoryId;
            $rules['name_en'] = 'required|max:255|unique:categories,name_en,' . $categoryId;
            $rules['slug'] = 'required|max:255|unique:categories,slug,' . $categoryId;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute không được để trống',
            'name_en.required' => ':attribute không được để trống',
            'slug.required' => ':attribute không được để trống',
            'title.required' => ':attribute không được để trống',
            'meta_title.required' => ':attribute không được để trống',
            'meta_description.required' => ':attribute không được để trống',
            'meta_keyword.required' => ':attribute không được để trống',
            'status.required' => ':attribute không được để trống',

            'name.max' => ':attribute tối đa 255 ký tự',
            'name_en.max' => ':attribute tối đa 255 ký tự',
            'title.max' => ':attribute tối đa 255 ký tự',
            'meta_title.max' => ':attribute tối đa 255 ký tự',
            'meta_keyword.max' => ':attribute tối đa 255 ký tự',

            'name.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'name_en.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'slug.unique' => ':attribute đã tồn tại vui lòng chọn tên khác',
            'slug.alpha_dash' => ':attribute phải là chuỗi chữ, số, dấu gạch ngang, vd: duong-dan-hi-hi',

        ];
    }


    public function attributes(): array
    {
        return [
            'name' => 'Tên danh mục',
            'slug' => 'Đường dẫn',
            'title' => 'Tiêu đề',
            'meta_title' => 'Tiêu đề meta',
            'meta_description' => 'Miêu tả meta',
            'meta_keyword' => 'Từ khóa meta',
            'status' => 'Trạng thái',
        ];
    }
}
