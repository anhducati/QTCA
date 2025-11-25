<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CategoryRequest;
use App\Repositories\Interfaces\CategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        session()->flash('active', 'category');

        $data = [
            'categories' => $this->categoryRepository->getAllCategory(),
        ];

        return view('backend.category.list', $data);
    }

    public function create_category()
    {
        return view('backend.category.create');
    }

    public function handle_create_category(CategoryRequest $request)
    {
        $data = [
            'name' => trim($request->name),
            'name_en' => trim($request->name_en ),
            'slug' => trim($request->slug),
            'title' => trim($request->title),
            'meta_title' => trim($request->meta_title),
            'meta_description' => trim($request->meta_description),
            'meta_keyword' => trim($request->meta_keyword),
            'status' => trim($request->status),
            'is_menu' => $request->is_menu,
            
        ];

        $result = $this->categoryRepository->createCategory($data);

        if($result) {
            return redirect()->route('admin.category.index')->with('msg-success', 'Thêm danh mục thành công');
        }

        return redirect()->back()->with('msg-error', 'Thêm danh mục thất bại');
    }

    public function update_category($id)
    {
        $data = [
            'category' => $this->categoryRepository->getCategoryById($id),
        ];

        return view('backend.category.update', $data);
    }

    public function handle_update_category(CategoryRequest $request, $id)
    {
//        $data = [
//            'name' => trim($request->name),
//            'slug' => trim(Str::slug($request->slug)),
//            'title' => trim($request->title),
//            'meta_title' => trim($request->meta_title),
//            'meta_description' => trim($request->meta_description),
//            'meta_keyword' => trim($request->meta_keyword),
//            'status' => trim($request->status),
//        ];

        $result = $this->categoryRepository->updateCategory($request->all(), $id);

        if($result) {
            return redirect()->route('admin.category.index', $id)->with('msg-success', 'Cập nhật danh mục thành công');
        }

        return redirect()->back()->with('msg-error', 'Cập nhật danh mục thât bại');
    }

    public function delete_category($id)
    {
        // $category = $this->categoryRepository->getCategoryById($id);
        // $category->is_delete = 1;
        // $category->save();
        $result = $this->categoryRepository->forceDelete($id);

        if ($result) {
            return redirect()->back()
                ->with('msg-success', "Xóa danh mục thành công");
        } else {
            return redirect()->back()
                ->with('msg-error', "óa danh mục thất bại");
        }


    }
}
