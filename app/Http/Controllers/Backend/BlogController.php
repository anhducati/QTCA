<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\BlogRequest;
use App\Http\Requests\Backend\BlogUpdateRequest;
use App\Models\Blog;
use App\Models\BlogTag;
use App\Repositories\Interfaces\BlogInterface;
use App\Repositories\Interfaces\CategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlogController extends Controller
{
    public $blogRepository;
    public $categoryRepository;
    public function __construct(BlogInterface $blogRepository, CategoryInterface $categoryRepository)
    {
        $this->blogRepository = $blogRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function blog(): View
    {
        session()->flash('active', 'blog');

        // if(Auth::user()->is_admin) {
        //     $data = [
        //         'blogs' => $this->blogRepository->getAllBlog(),
        //     ];
        // }else {
        //     $data = [
        //         'blogs' => $this->blogRepository->getBlogByUserId(Auth::user()->id),
        //     ];
        // }

        $data = [
                'blogs' => $this->blogRepository->getAllBlog(),
        ];


        return view('backend.blog.list', $data);
    }

    public function recycle(): View
    {
        $data = [
            'blogs' => $this->blogRepository->getAllRecycleBlog(),
        ];


        return view('backend.blog.recycle', $data);
    }

    public function create_blog()
    {

        $data = [
            'categories' => $this->categoryRepository->getAllCategory(),
        ];

        return view('backend.blog.create', $data);
    }

    public function handle_create_blog(BlogRequest $request)
    {


        $blog = new Blog();
        $blog->title =          trim($request->title);
        $blog->title_en =          trim($request->title_en);
        $blog->user_id =        Auth::user()->id;
        $blog->category_id  =   trim($request->category_id);
        $blog->slug  =          trim($request->slug);
        $blog->content  =       trim($request->contents);
        $blog->content_en  =       trim($request->contents_en);
        $blog->meta_description  = trim($request->meta_description);
        $blog->meta_keyword  = trim($request->meta_keyword);
        $blog->is_publish  =    trim($request->is_publish);
        // $blog->status  =        trim($request->status);
        $blog->save();

        if($request->hasFile('image_file')) {
            $ext = $request->file('image_file')->getClientOriginalExtension();
            $file = $request->file('image_file');
            $fileName = $request->slug . '.' . $ext;
            $uploadsPath = public_path('upload/blog/');
            $file->move($uploadsPath , $fileName);

            $blog->image_file = $fileName;
        }

        $blog->save();

        BlogTag::InsertDeleteTags($blog->id, $request->tags);


        return redirect()->route('admin.blog')
            ->with('msg-success', 'Thêm bài viết thành công');
    }

    public function update_blog($id)
    {

        $blog = $this->blogRepository->getBlogById($id);
        if(!isset($blog) && !is_object($blog)) {
            abort(404);
        }

        $data = [
            'categories' => $this->categoryRepository->getAllCategory(),
            'blog' => $blog,
            'list_tags' => BlogTag::getTagByBlogId($id),
        ];


        return view('backend.blog.update', $data);
    }

    public function handle_update_blog(BlogUpdateRequest $request, $id)
    {
//        dd($request->tags);
        $result = $this->blogRepository->updateBlog($request->all(), $id);


        if($result) {
            return redirect()->route('admin.blog.update', $id)->with('msg-success', 'Cập nhật bài viết thành công');
        }

        return redirect()->back()->with('msg-error', 'Cập nhật bài viết thấtt bại');

    }

    public function delete_blog($id)
    {
        $result = $this->blogRepository->softDelete($id);

        if ($result) {
            return redirect()->back()
                ->with('msg-success', "Bài viết đã được đưa vào thùng rác");
        } else {
            return redirect()->back()
                ->with('msg-error', "Bài viết đã được đưa vào thùng rác");
        }
    }

    public function restore($id)
    {
        $result = $this->blogRepository->restoreBlog($id);

        if ($result) {
            return redirect()->route('admin.blog')
                ->with('msg-success', "Bài viết đã khôi phục thành công");
        } else {
            return redirect()->back()
                ->with('msg-error', "Khôi phục bài viêt không thành công");
        }
    }

    public function force_delete($id)
    {
        $check = $this->blogRepository->getBlogById($id);
        if($check->is_delete == 1) {
            $result = $this->blogRepository->forceDelete($id);

            if ($result) {
                return redirect()->back()
                    ->with('msg-success', "Bài viết đã được xóa vĩnh viễn");
            } else {
                return redirect()->back()
                    ->with('msg-error', "Bài viết đã được xóa vĩnh viễn");
            }

        }else {
            return redirect()->back()
                ->with('msg-error', "Xóa thất bại do bài viết không tồn tại trong thùng rác");
        }


    }
}
