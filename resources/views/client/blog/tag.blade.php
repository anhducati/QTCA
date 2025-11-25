@extends('layouts.client')

@section('main')
    <!-- ====== start nav search ====== -->
    <div class="tc-blog-page">
        <div class="tc-blog-nav-search" style="padding: 10px 0; padding-bottom: 0px;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="info pt-30">
                            <h4>Tag: {{ $tagName }}</h4>
                        </div>
                    </div>
                    {{-- <div class="col-lg-5">
                        <form class="search-form" method="get" action="{{ route('client.blog.search') }}">
                            <div class="form-group">
                                <input type="text" name="query" placeholder="Tìm kiếm bài viết">
                                <button type="submit"> <i class="la la-search"></i> </button>
                            </div>
                        </form>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- ====== end nav search ====== -->


    <main>



        <!-- ====== start popular posts ====== -->
        <section class="tc-popular-posts-blog">
            <div class="container">

                <div class="content-widgets pt-50 pb-50">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="tc-post-list-style3 mb-50">
                                {{-- <div class="item mb-30 p-30 bg-gray1 border-bottom-0">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <div class="img th-180 img-cover overflow-hidden">
                                                <img src="{{ asset('assets/client/img/sponsored/6.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="content mt-20 mt-lg-0">
                                                <a href="#"
                                                   class="color-main fsz-13px text-uppercase mb-10">Nội dung được tài trợ
                                                </a>
                                                <h4 class="title fw-bold">
                                                    <a href="page-single-post-creative.html" class="hover-underline">
                                                        iPad Air 4G GPS, Wifi + LTE, 512GB, Dark Grey
                                                    </a>
                                                </h4>
                                                <div class="meta-bot fsz-13px">
                                                    <a href="#"> itechstore.com <i
                                                            class="las la-external-link-square-alt ms-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="items">

                                    @if(isset($blogByTag) && is_object($blogByTag))
                                        @foreach($blogByTag as $value)
                                            <div class="item">
                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <div class="img th-230 img-cover overflow-hidden">
                                                            <a href="{{ route('client.blog.detail', $value->slug) }}">
                                                                <img class="image-blog" src="{{ asset('upload/blog/'.$value->image_file) }}" alt="{{ $value->title }}">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <div class="content mt-20 mt-lg-0">
                                                            <a href="#1"
                                                               class="color-999 fsz-13px text-uppercase mb-10">{{ $value->category_name }}</a>
                                                            <h4 class="title mb-15">
                                                                <a href="{{ route('client.blog.detail', $value->slug) }}">
                                                                    {{ $value->title }}
                                                                </a>
                                                            </h4>
                                                            <div class="text color-666 mb-0">


                                                                @php
                                                                    $content = Illuminate\Support\Str::limit(strip_tags(html_entity_decode($value->content)), 140, ' [...]');
                                                                @endphp

                                                                <a href="{{ route('client.blog.detail', $value->slug) }}">
                                                                    {!! $content !!}

                                                                </a>

                                                                {{--                                                        <a class="text-danger" href="{{ route('client.blog.detail', $value->slug) }}">Xem chi tiết</a>--}}



                                                            </div>
                                                            <div class="meta-bot fsz-13px color-666">
                                                                <ul class="d-flex">
                                                                    <li class="date me-5">
                                                                        <a href="#">
                                                                            <i class="la la-calendar me-2"></i>
                                                                            {{ date('d-m-Y H:i', strtotime($value->blog_created_at)) }}

                                                                        </a>
                                                                    </li>
                                                                    <li class="author me-5">
                                                                        <a href="#"><i class="la la-user me-2"></i> by
                                                                            <span class="color-000">
                                                                        {{ $value->user_name }}
                                                                    </span>
                                                                        </a>
                                                                    </li>
                                                                    <li class="comment">
                                                                        <a href="#"><i class="la la-comment me-2"></i> 0
                                                                            Bình luận</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        @endforeach
                                    @endif



                                </div>
                            </div>

                            {{ $blogByTag->links('vendor.pagination.custom') }}


                        </div>
                        
                         <!-- Sidebar -->
                        <div class="col-lg-3">
                            <div class="widgets-sticky mt-5 mt-lg-0">
                                <!-- Danh mục -->
                                <div class="tc-widget-tags-style5 mb-40">
                                    <p class="color-000 text-uppercase mb-30"> Danh mục bài viết </p>
                                    <div class="tags-content">
                                        @if(isset($listCategory) && is_object($listCategory))
                                            @foreach($listCategory as $value)
                                                <a href="{{ route('client.blog.category', ['slug' => $value->slug] ) }}">{{ $value->name }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!-- end Danh mục -->
                                

                                <div class="tc-post-list-style2 pb-40">
                                    <p class="color-000 text-uppercase mb-30"> Bài viết mới nhất </p>
                                    <div class="items">

                                        @if(isset($blogWidgets) && is_object($blogWidgets))
                                        @foreach($blogWidgets as $value)
                                            <a href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}"
                                            class="item d-block border-1 border-top border-bottom-0 brd-gray pt-15 mt-15">
                                                <div class="row gx-3 align-items-center">
                                                    <div class="col-4">
                                                        <div class="img th-50 img-cover">
                                                            <img src="{{ asset('upload/blog/'.$value->image_file) }}" alt="{{ $value->slug }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="content">
                                                            <h6 class="title ltspc--1">
                                                                @php
                                                                    $title = Str::limit($value->title, 40, '...');
                                                                @endphp


                                                                {!! $title !!}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            @endforeach
                                        @endif

                                        
                                    </div>
                                </div>



                                <!-- widget tags -->
                                <div class="tc-widget-tags-style5 mb-40">
                                    <p class="color-000 text-uppercase mb-30"> Hot Tags Hôm nay </p>
                                    <div class="tags-content">
                                        @if(isset($blogTagWidgets) && is_object($blogTagWidgets))
                                            @foreach($blogTagWidgets as $value)
                                                <a href="{{ route('client.blog.tag', ['name' => $value->name ]) }}">{{ $value->name }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!-- end widget tags -->

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- ====== end popular posts ====== -->

        <!-- ====== start modals ====== -->

         <div class="offcanvas offcanvas-start sidebar-popup-style1" tabindex="-1" id="offcanvasExample"
             aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <div class="logo">
                    <img src="{{ asset('assets/client/img/logo_home1.png') }}" alt="" class="dark-none">
                    <img src="{{ asset('assets/client/img/logo_home1_lt.png') }}" alt="" class="light-none">
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mt-4">
                
                <div class="text">
                    Tranng tin y tế số 1 Việt Nam
                </div>

                <div class="sidebar-categories mt-40">
                    <h6 class="color-000 text-uppercase mb-30 ltspc-1"> Danh mục <i
                            class="la la-angle-right ms-1"></i> </h6>
                       
                    @if(isset($listCategory) && is_object($listCategory))
                        @foreach($listCategory as $value)
                            <a href="{{ route('client.blog.category', ['slug' => $value->slug] ) }}" class="cat-card">
                                <div class="img img-cover">
                                    <img src="{{ asset('assets/client/img/videos/1.png') }}" alt="">
                                </div>
                                <div class="info">
                                    <h5>{{ $value->name }}</h5>
                                    
                                </div>
                            </a>
                        @endforeach
                    @endif

                </div>
                
            </div>
        </div>
        <!-- ====== end modals ====== -->

    </main>
@endsection

@section('style')
    <style>
        .image-blog {
            bottom: 0;
            font-family: "object-fit: cover;";
            height: 100%;
            left: 0;
            -o-object-fit: cover;
            object-fit: cover;
            -o-object-position: 50% 50%;
            object-position: 50% 50%;
            position: absolute;
            right: 0;
            top: 0;
            width: 100%;
        }
    </style>
@endsection
