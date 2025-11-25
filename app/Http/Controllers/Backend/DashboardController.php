<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Repositories\Interfaces\BlogInterface;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public $blogRepository;
    
    public function __construct(BlogInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    public function index()
    {
        // session()->flash('active', 'dashboard');

        // return view('backend.dashboard.index');

        session()->flash('active', 'blog');

        if(Auth::user()->is_admin) {
            $data = [
                'blogs' => $this->blogRepository->getAllBlog(),
            ];
        }else {
            $data = [
                'blogs' => $this->blogRepository->getBlogByUserId(Auth::user()->id),
            ];
        }




        return view('backend.blog.list', $data);

    }
}
