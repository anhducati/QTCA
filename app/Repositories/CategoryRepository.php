<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryInterface;
use Illuminate\Database\Schema;

class CategoryRepository implements CategoryInterface
{

    public function getCategoryById(int $id)
    {
        return Category::find($id);
    }

    public function getAllCategory()
    {
        return Category::where('is_delete' , '=', 0)
            ->orderBy('categories.id', 'desc')->get();
    }

    public function getAllBySlug(string $slug)
    {
        return Category::where('is_delete' , '=', 0)
            ->where('slug' , '=', $slug)
            ->first();
    }


    public function createCategory(array $data)
    {
        $category = new Category();
        $category->name = $data['name'];
        $category->slug = $data['slug'];
        $category->title = $data['title'];
        $category->meta_title = $data['meta_title'];
        $category->meta_description = $data['meta_description'];
        $category->meta_keyword = $data['meta_keyword'];
        $category->status = $data['status'];
        $category->is_menu = $data['is_menu'];
        $category->name_en = $data['name_en'];

        // Lưu đối tượng Category vào cơ sở dữ liệu và kiểm tra xem việc lưu thành công hay không
        if ($category->save()) {
            // Trả về true nếu lưu thành công
            return true;
        } else {
            // Trả về false nếu có lỗi xảy ra khi lưu
            return false;
        }
    }

    public function updateCategory(array $data, $categoryId)
    {
        // Tìm đối tượng Category cần cập nhật
        $category = Category::findOrFail($categoryId);

        // Cập nhật thông tin của đối tượng Category từ dữ liệu được cung cấp
        $category->name = $data['name'];
        $category->slug = $data['slug'];
        $category->title = $data['title'];
        $category->meta_title = $data['meta_title'];
        $category->meta_description = $data['meta_description'];
        $category->meta_keyword = $data['meta_keyword'];
        $category->status = $data['status'];
        $category->is_menu = $data['is_menu'];
        $category->name_en = $data['name_en'];

        // Lưu các thay đổi vào cơ sở dữ liệu và kiểm tra xem việc cập nhật thành công hay không
        if ($category->save()) {
            // Trả về true nếu cập nhật thành công
            return true;
        } else {
            // Trả về false nếu có lỗi xảy ra khi cập nhật
            return false;
        }
    }

    public function forceDelete($categoryId) 
    {
        $category = Category::find($categoryId);

        if($category->forceDelete()) {
            return true;
        }

        return false;
        
    }
}
