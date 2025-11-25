<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\BlogInterface;
use App\Repositories\Interfaces\CategoryInterface;
use Illuminate\Http\Request;
use App\Models\Major;
use App\Models\AdmissionSubject;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Request as Req;


class HomeController extends Controller
{
    protected $blogRepository;
    protected $categoryRepository;

    public function __construct(BlogInterface $blogRepository, CategoryInterface $categoryRepository)
    {
        $this->blogRepository = $blogRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function home()
    {
        // session()->flash('title-page', 'Đại Học Thủy Lợi');
        $locale = App::getLocale(); 
            if ($locale == 'en') {
                session()->flash('title-page', 'Water Resources University');
            } elseif ($locale == 'vi') {
                session()->flash('title-page', 'Đại Học Thủy Lợi');
            }
        session()->flash('meta-keywords', 'Đại Học Thủy Lợi');
        session()->flash('meta-description', 'Tin tức Đại Học Thủy Lợi,đời sống, học phí');
        session()->flash('author', 'Đại Học Thủy Lợi');

        $listMenu = Category::where('is_menu', 1)
            ->where('is_delete', 0)
            ->orderBy('id', 'desc')
            ->get();
        session()->put('listMenu', $listMenu);

        $listSubMenu = Category::where('is_menu', 2)
            ->where('is_delete', 0)
            ->orderBy('id', 'desc')
            ->get();
        session()->put('listSubMenu', $listSubMenu);

        $blogNew =  $this->blogRepository->getAllBlog();

        // Hiển thị đầu trang
        $priorityBlog = $this->blogRepository->getBlogByIdWithDetails(29);

        // sidebar
        $blogTagWidgets =  $this->blogRepository->getAllTagLimit(30);
        $blogWidgets =  $this->blogRepository->getBlogLimit(6);
        $listCategory = $this->categoryRepository->getAllCategory();

        $blogByView = $this->blogRepository->getBlogByView();

        $blogLimitNew = $this->blogRepository->getBlogByCategoryIdLimit(3, 6);
        $blogLimitOld = $this->blogRepository->getBlogByCategoryIdLimit(3, 6, 'asc');

        $majors = Major::getAllMajorLimit(6);

        

        $data = [
            'blogNew' => $blogNew,
            'priorityBlog' => $priorityBlog,
            'blogWidgets' => $blogWidgets,
            'blogByView' => $blogByView,
            'blogLimitNew' => $blogLimitNew,
            'blogLimitOld' => $blogLimitOld,
            'blogTagWidgets' => $blogTagWidgets,
            'listCategory' => $listCategory,
            'majors' => $majors,
        ];

        return view('client.home.home', $data );
    }

    public function blogDetail($slug)
    {
        // sidebar
        $blogWidgets =  $this->blogRepository->getBlogLimit(6);
        $blogTagWidgets =  $this->blogRepository->getAllTagLimit(10);
        $listCategory = $this->categoryRepository->getAllCategory();

        $blogDetail = $this->blogRepository->getBlogBySlug($slug);
        $blogTags = $this->blogRepository->getBlogTag($blogDetail->id);

        $listBlogs =  $this->blogRepository->getAllBlog();

        if(!empty($blogDetail)) {
            session()->flash('title-page', $blogDetail->title);
            session()->flash('meta-keywords', $blogDetail->meta_keyword);
            session()->flash('meta-description', $blogDetail->meta_description);
            session()->flash('author', $blogDetail->user_name);

//          Tăng lượt xem bài viết
            $this->blogRepository->updateViewBlog($blogDetail->id);

            $data = [
                'blog' => $blogDetail,
                'blogTags' => $blogTags,
                'listCategory' => $listCategory,
                'blogWidgets' => $blogWidgets,
                'blogTagWidgets' => $blogTagWidgets,
                'listBlogs' => $listBlogs
            ];


            return view('client.blog.single', $data);
        }else {
            abort(404);
        }
    }

    public function search()
    {
        $query = Req::get('query');
        $result = $this->blogRepository->searchBog($query, 10);

        // sidebar
        $blogWidgets =  $this->blogRepository->getBlogLimit(6);
        $blogTagWidgets =  $this->blogRepository->getAllTagLimit(10);
        $listCategory = $this->categoryRepository->getAllCategory();

        $data = [
            'blogSearch' => $result,
            'listCategory' => $listCategory,
            'blogWidgets' => $blogWidgets,
            'blogTagWidgets' =>  $blogTagWidgets,
        ];

        return view('client.blog.search', $data);
    }

    public function contact()
    {
        $listCategory = $this->categoryRepository->getAllCategory();

        $data = [
            
            'listCategory' => $listCategory,
        ];

        return view('client.home.contact', $data);
    }

    public function blogDetails()
    {
        return view('client.blog.single2');
    }

    public function blogCategory($slug)
    {
        // sidebar
        $blogWidgets =  $this->blogRepository->getBlogLimit(6);
        $blogTagWidgets =  $this->blogRepository->getAllTagLimit(10);
        $listCategory = $this->categoryRepository->getAllCategory();

        $category = $this->categoryRepository->getAllBySlug($slug);
        $blogNewWidget = $this->blogRepository->getBlogLimit('8');

        if(!empty($category) && is_object($category)) {
            $locale = App::getLocale(); 
            if ($locale == 'en') {
                Session::flash('title-page', $category->name_en);
            } elseif ($locale == 'vi') {
                Session::flash('title-page', $category->name);
            }
            $blogByCate = $this->blogRepository->getBlogByCategoryIdPagination($category->id, 10);
        }

        if(!empty($blogByCate) && is_object($blogByCate)) {
            $data = [
                'blogByCate' => $blogByCate,
                'blogNewWidget' => $blogNewWidget,
                'category' => $category,
                'listCategory' => $listCategory,
                'blogWidgets' => $blogWidgets,
                'blogTagWidgets' =>  $blogTagWidgets,
            ];

            return view('client.blog.category', $data);
        }else {
            abort(404);
        }


    }

    public function blogTag($name)
    {
        $blogByTag = $this->blogRepository->getBlogByBlogTagName($name, 8);

        $blogNewWidget = $this->blogRepository->getBlogLimit('8');

        // $listCategory = $this->categoryRepository->getAllCategory();
        // sidebar
        $blogTagWidgets =  $this->blogRepository->getAllTagLimit(30);
        $blogWidgets =  $this->blogRepository->getBlogLimit(6);
        $listCategory = $this->categoryRepository->getAllCategory();

        if(!empty($blogByTag) && is_object($blogByTag)) {
            $data = [
                'blogByTag' => $blogByTag,
                'blogNewWidget' => $blogNewWidget,
                'tagName' => $name,
                'listCategory' => $listCategory,
                'blogTagWidgets' => $blogTagWidgets,
                'blogWidgets' => $blogWidgets,

            ];

            return view('client.blog.tag', $data);
        }else {
            abort(404);
        }
    }

    public function major(){
        $data = [       
            'majors' =>  Major::getAllMajorLimit(6) //Phân trang hiển thị nghanh đào tạo 6 ô
        ];
        $locale = App::getLocale(); 
        if ($locale == 'en') {
            Session::flash('title-page', 'Training industry');
        } elseif ($locale == 'vi') {
            Session::flash('title-page', 'Ngành đào tạo');
        }


        return view('client.major.list', $data);
    }

    public function major_detail($slug){

        $major = Major::getMajorBySly($slug);
        $subjects = AdmissionSubject::getSubjectByMajorId($major->id);
        // dd($subjects);
       
        $locale = App::getLocale(); 
        if ($locale == 'en') {
            Session::flash('title-page', $major->name_en);
        } elseif ($locale == 'vi') {
            Session::flash('title-page', $major->name);
        }
        
        $data = [       
            'major' =>  $major,
            'subjects' => $subjects,
        ];

        // dd($data);
        

        return view('client.major.single', $data);
    }

    // Học phí
    public function tuition($slug) {
        // sidebar
        $blogWidgets =  $this->blogRepository->getBlogLimit(6);
        $blogTagWidgets =  $this->blogRepository->getAllTagLimit(10);
        $listCategory = $this->categoryRepository->getAllCategory();

        $category = $this->categoryRepository->getAllBySlug($slug);
        $blogNewWidget = $this->blogRepository->getBlogLimit('8');

        if(!empty($category) && is_object($category)) {
            $blogByCate = $this->blogRepository->getBlogByCategoryIdPagination($category->id, 10);
        }

        if(!empty($blogByCate) && is_object($blogByCate)) {
            $data = [
                'blogByCate' => $blogByCate,
                'blogNewWidget' => $blogNewWidget,
                'category' => $category,
                'listCategory' => $listCategory,
                'blogWidgets' => $blogWidgets,
                'blogTagWidgets' =>  $blogTagWidgets,
            ];

            return view('client.blog.category', $data);
        }else {
            abort(404);
        }
    }
}
