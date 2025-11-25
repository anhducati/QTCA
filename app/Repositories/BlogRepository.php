<?php

namespace App\Repositories;

use App\Models\BlogTag;
use App\Repositories\Interfaces\BlogInterface;
use App\Models\Blog;
use Request;
class BlogRepository implements BlogInterface
{

    public function getBlogById(int $id)
    {
        return Blog::find($id);
    }

    public function getBlogByIdWithDetails(int $id)
    {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.is_publish' , '=', 1)
            // ->where('blogs.status' , '=', 1)
            ->where('blogs.id' , '=', $id)
            ->where('is_publish' , '=', 1)
            ->first();
    }

    // detail baiviet
    public function getBlogBySlug(string $slug)
    {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.slug' , '=', $slug)
            ->where('is_publish' , '=', 1)
            ->orderBy('blogs.id', 'desc')
            ->first();
    }

    public function getBlogTag($blog_id)
    {
        return BlogTag::where('blog_id', $blog_id)->get();
    }

    public function getAllTagLimit($limit)
    {
        return BlogTag::select('name')->distinct()->limit($limit)->get();
        // return BlogTag::limit($limit)->get();
    }

    public function getBlogByCategoryIdPagination(int $id, int $pagination)
    {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.category_id' , '=', $id)
            ->where('is_publish' , '=', 1)
            ->orderBy('blogs.id', 'desc')
            ->paginate($pagination);
    }

    public function getBlogByCategoryIdLimit(int $id, int $limit, $orderBy = 'desc') {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.is_publish' , '=', 1)

            // ->where('blogs.status' , '=', 1)
            ->where('blogs.category_id' , '=', $id)
            ->orderBy('blogs.id', $orderBy)
            ->limit($limit)
            ->get();
    }

    public function searchBog($query, $pagination) {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')
            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.title' , 'like', '%'.$query .'%')
            ->where('is_publish' , '=', 1)
            ->orderBy('blogs.id', 'desc')
            ->paginate($pagination);
    }

    public function getBlogByBlogTagName(string $name, int $paginate = 10)
    {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate', 'blogs.created_at as blog_created_at')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->join('blog_tags', 'blog_tags.blog_id', '=', 'blogs.id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blog_tags.name' , '=', $name)
            ->orderBy('blogs.id', 'desc')
            ->where('is_publish' , '=', 1)
            ->paginate($paginate);
    }


    public function getBlogByCategoryId(int $id)
    {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate', 'blogs.created_at as blog_created_at')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.category_id' , '=', $id)
            ->where('is_publish' , '=', 1)
            ->orderBy('blogs.id', 'desc')
            ->get();
    }

    public function getAllBlog()
    {

//        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name')
//            ->join('users', 'users.id', '=', 'blogs.user_id')
//            ->join('categories', 'categories.id', '=', 'blogs.category_id')
//            ->where('blogs.is_delete' , '=', 0)
//            ->orderBy('blogs.id', 'desc')
//            ->get();

        $return = Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN')
            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id');

            if(!empty(Request::get('title'))) {
                $return = $return->where('blogs.title' , 'LIKE', '%'. Request::get('title') .'%');
            }

            if(!empty(Request::get('username'))) {
                $return = $return->where('users.name' , 'LIKE', '%'. Request::get('username') .'%');
            }

            if(!empty(Request::get('is_publish'))) {
                $is_publish = Request::get('is_publish');
                if($is_publish == 100) {
                    $is_publish = 0;
                }
                $return = $return->where('blogs.is_publish' , '=' , $is_publish );
            }

            // if(!empty(Request::get('status'))) {
            //     $status = Request::get('status');
            //     if($status == 100) {
            //         $status = 0;
            //     }
            //     $return = $return->where('blogs.status' , '=' , $status);
            // }

            if(!empty(Request::get('start_date'))) {

                $return = $return->whereDate('blogs.created_at' , '>=' , Request::get('start_date') );
            }

            if(!empty(Request::get('end_date'))) {

                $return = $return->whereDate('blogs.created_at' , '<=' , Request::get('end_date') );
            }

            $return = $return->where('blogs.is_delete' , '=', 0)
            ->orderBy('blogs.id', 'desc')
            ->get();

        return $return;
    }

    public function getBlogByUserId($user_id)
    {

        $return = Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN')
            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('users.id' , '=', $user_id);

        if(!empty(Request::get('title'))) {
            $return = $return->where('blogs.title' , 'LIKE', '%'. Request::get('title') .'%');
        }

        if(!empty(Request::get('username'))) {
            $return = $return->where('users.name' , 'LIKE', '%'. Request::get('username') .'%');
        }

        if(!empty(Request::get('is_publish'))) {
            $is_publish = Request::get('is_publish');
            if($is_publish == 100) {
                $is_publish = 0;
            }
            $return = $return->where('blogs.is_publish' , '=' , $is_publish );
        }

        // if(!empty(Request::get('status'))) {
        //     $status = Request::get('status');
        //     if($status == 100) {
        //         $status = 0;
        //     }
        //     $return = $return->where('blogs.status' , '=' , $status);
        // }

        if(!empty(Request::get('start_date'))) {

            $return = $return->whereDate('blogs.created_at' , '>=' , Request::get('start_date') );
        }

        if(!empty(Request::get('end_date'))) {

            $return = $return->whereDate('blogs.created_at' , '<=' , Request::get('end_date') );
        }

        $return = $return->where('blogs.is_delete' , '=', 0)
            ->orderBy('blogs.id', 'desc')
            ->get();

        return $return;
    }

    public function getBlogLimit(int $limit, $orderBy = 'desc') {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.is_publish' , '=', 1)
            // ->where('blogs.status' , '=', 1)
            ->where('is_publish' , '=', 1)
            ->orderBy('blogs.id', $orderBy)
            ->limit($limit)
            ->get();
    }

    public function getBlogByView(int $limit = 10) {
        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN'
            , 'categories.slug as category_slug', 'categories.id as id_cate')

            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=', 0)
            ->where('blogs.is_publish' , '=', 1)
            // ->where('blogs.status' , '=', 1)
            ->orderBy('blogs.id', 'asc')
            ->where('is_publish' , '=', 1)
            ->limit($limit)
            ->get();
    }

    public function getAllRecycleBlog()
    {

        return Blog::select('blogs.*', 'users.name as user_name', 'categories.name as category_name','categories.name_en as category_nameEN')
            ->join('users', 'users.id', '=', 'blogs.user_id')
            ->join('categories', 'categories.id', '=', 'blogs.category_id')
            ->where('blogs.is_delete' , '=',1)
            ->where('is_publish' , '=', 1)
            ->orderBy('blogs.id', 'desc')
            ->get();
    }

    public function createBlog(array $data)
    {

    }

    /**
     * @param $blogId
     * @return bool
     */
    public function updateViewBlog($id)
    {
        try {
            // Tìm đối tượng Blog cần cập nhật
            $blog = Blog::findOrFail($id);

            // Kiểm tra xem bài viết có tồn tại không
            if (!$blog) {
                throw new \Exception("Không tìm thấy bài viết với ID: $id");
            }

            // Tăng lượt xem
            $blog->increment('view');

            // Lưu thay đổi vào cơ sở dữ liệu
            $blog->save();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateBlog(array $data, $blogId)
    {
        // Tìm đối tượng Category cần cập nhật
        $blog = Blog::findOrFail($blogId);

        // Cập nhật thông tin của đối tượng Category từ dữ liệu được cung cấp
        $blog->title = $data['title'];
        $blog->title_en = $data['title_en'];
        $blog->category_id = $data['category_id'];
        $blog->slug = $data['slug'];
        $blog->content = $data['contents'];
        $blog->content_en = $data['contents_en'];
        $blog->meta_description = $data['meta_description'];
        $blog->meta_keyword = $data['meta_keyword'];
        $blog->is_publish = $data['is_publish'];
        // $blog->status = $data['status'];
        $blog->updated_at = date('Y-m-d H:i:s');
        if(!empty($data['image_file'])) {
//            $blog->image_file = $data['image_file'];

            $ext = $data['image_file']->getClientOriginalExtension();
            $fileName = $data['slug'] . '.' . $ext;
            $uploadsPath = public_path('upload/blog/');
            $data['image_file']->move($uploadsPath, $fileName);

            $blog->image_file = $fileName;
        }

        // Lưu các thay đổi vào cơ sở dữ liệu và kiểm tra xem việc cập nhật thành công hay không
        if ($blog->save()) {
            // Trả về true nếu cập nhật thành công
            BlogTag::InsertDeleteTags($blog->id, $data['tags']);
            return true;
        } else {
            // Trả về false nếu có lỗi xảy ra khi cập nhật
            return false;
        }
    }


    /**
     * @param $blogId
     * @return mixed
     */
    public function softDelete($blogId) : mixed
    {
        $blog = $this->getBlogById($blogId);
        $blog->is_delete = 1;
        $result = $blog->save();

        return $result;
    }

    public function restoreBlog($blogId) {
        $blog = $this->getBlogById($blogId);
        $blog->is_delete = 0;
        $result = $blog->save();

        return $result;
    }

    public function forceDelete($blogId) : mixed
    {
        $blog = $this->getBlogById($blogId);

        if($blog->is_delete == 1) {
            $blog->forceDelete();
            return true;
        }

        return true;
    }
}
