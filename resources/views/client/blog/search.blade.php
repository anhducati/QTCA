@extends('layouts.client')

@section('main')
    <!-- ====== start nav search ====== -->
    <div class="tc-blog-page">
        <div class="tc-blog-nav-search">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="info pt-40">

                            @if(count($blogSearch) == 0)
                                <h5>Không tìm thấy kết quả cho: {{ Request::get('query') }}</h5>
                            @else
                                <h5>Kết quả tìm kiếm của: {{ Request::get('query') }}</h5>
                            @endif
                        </div>
                    </div>
                    {{-- <div class="col-lg-5">
                        <form class="search-form" method="get" action="{{ route('client.blog.search') }}">
                            <div class="form-group">
                                <input type="text" name="query" placeholder="Tim kiếm bài viết">
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
                            <div class="tc-post-list-style3">
                          <div class="items">

                                    @if(isset($blogSearch) && is_object($blogSearch))
                                        @foreach($blogSearch as $value)
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
                                                                            {{ date('Y-m-d H:i:s', strtotime($value->created_at)) }}

                                                                        </a>
                                                                    </li>
                                                                    <li class="author me-5">
                                                                        <a href="#"><i class="la la-user me-2"></i> Tác giả:
                                                                            <span class="color-000">
                                                                        {{ $value->user_name }}
                                                                    </span>
                                                                        </a>
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


                            {{ $blogSearch->links('vendor.pagination.custom') }}





                        </div>
                        

                        <!-- Sidebar -->
                        <div class="col-lg-3">
                            <div class="widgets-sticky mt-5 mt-lg-0">
                                
                                <!-- Danh mục -->
                                <div class="tc-widget-tags-style5 mb-40">
                                    <p class="color-000 text-uppercase mb-30">{{ __('messages.danhmucbaiviet') }}</p>
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
                                    <p class="color-000 text-uppercase mb-30">{{ __('messages.baivietmoi') }}</p>
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
                                    <p class="color-000 text-uppercase mb-30">{{ __('messages.hottag') }}</p>
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

