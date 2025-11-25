@extends('layouts.client')

@section('main')
    <main>

        
        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://www.tlu.edu.vn/Portals/0/banner-chao-nam-hocthumb.jpg" class="d-block w-100"
                        alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://www.tlu.edu.vn/Portals/0/2024/Thang4/bannerts1thumb.jpg" class="d-block w-100"
                        alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://www.tlu.edu.vn/Portals/0/banner-chao-nam-hocthumb.jpg" class="d-block w-100"
                        alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <section class="tc-latest-news-style1">
            <div class="container">
                <div class="section-content pt-10 border-bottom border-1 brd-gray">   
                    <div class="row mb-4">
                        <div class="col-12">
                            <h3 class="text-center title-h3">MAP</h3>
                        </div>
                        {{-- <h3 class="text-center title-h3">MAP</h3> --}}
                    </div>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div id="map"></div>
                        </div>
                    </div>
                    <script src="{{ asset('assets/client/js/map.js') }}"></script>
                    <div class="row mb-4 mt-4">
                        <div class="col-12">
                            <h3 class="text-center title-h3">{{ __('messages.majors') }}</h3>
                        </div>

                    </div>

                    
                    <div class="row mb-4 mt-4">
                        @if(isset($majors) && is_object($majors))
                            @foreach($majors as $value)
                                <div class="col-lg-4 col-md-6 col-sm-12 d-flex justify-center ">
                                    <a href="{{ route('client.major.detail', ['slug' => $value->slug]) }}" class="d-inline-block mx-auto">
                                        @if(isset($value->image_file))

                                            <img src="{{ asset('upload/major/' . $value->image_file) }}" alt="{{ $value->slug }}" >

                                        @endif
                                        {{-- <img src="{{ asset('assets/client/img/cntt-clc.jpg') }}" alt=""> --}}
                                        
                                        
                                        @if(app()->getLocale() == 'en')
                                        <p class="box-title-nganh">{{ $value->name_en }}</p>
                                         @elseif(app()->getLocale() == 'vi')
                                         <p class="box-title-nganh">{{ $value->name }}</p>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        @endif

                        {{-- <div class="col-lg-5">
                            
                        </div> --}}

                        @if(count($majors) >=3)
                            <div class="col-12 d-flex justify-content-center mt-2">
                                <a href="{{ route('client.major') }}" class="btn btn-primary" class="d-inline-block mx-auto" >{{ __('messages.xemthem') }}</a>
                            </div>
                        @endif
                        
                        {{-- <div class="col-lg-5">
                            
                        </div> --}}

                        

                    </div>

                </div>
            </div>
        </section>


        



        <!-- ====== start Latest news ====== -->
        <section class="tc-latest-news-style1">
            <div class="container">
                <div class="section-content pt-15 pb-20 border-bottom border-1 brd-gray">
                    <p class="color-000 text-uppercase mb-20 ltspc-1"> <a href="#1">{{ __('messages.tinmoinhat') }}</a> <i class="la la-angle-right ms-1"></i>
                    </p>
                    <div class="row">
                        @if(isset($priorityBlog) && is_object($priorityBlog))
                        <div class="col-lg-8 border-end brd-gray border-1">
                            <div class="tc-post-grid-default">
                                <div class="item">

                                    <div class="img img-cover th-330 overflow-hidden">

                                        <a href="{{ route('client.blog.detail', ['slug' => $priorityBlog->slug]) }}">
                                            <img class="image-blog" src="{{ asset('upload/blog/'.$priorityBlog->image_file) }}" alt="{{ $priorityBlog->slug }}">
                                        </a>

                                    </div>

                                    <div class="content pt-20">
                                        <a href="{{ route('client.blog.category', ['slug' => $priorityBlog->category_slug]) }}"
                                           class="news-cat color-999 fsz-13px text-uppercase mb-10">{{ app()->getLocale() === 'en' ? $priorityBlog->category->name_en : $priorityBlog->category_name }}</a>
                                        <h2 class="title mb-20">
                                            <a href="{{ route('client.blog.detail', ['slug' => $priorityBlog->slug]) }}">
                                                {{-- {{    Str::limit($priorityBlog->title, 100, '...') }} --}}
                                                {{--  --}}
                                                {{ Str::limit(app()->getLocale() === 'en' ? $priorityBlog->title_en : $priorityBlog->title, 100, '...') }}

                                    </a>
                                </h2>
                                <div class="text color-666">
                                    @php
                                        $content = Str::limit(strip_tags(html_entity_decode($priorityBlog->content)), 300, ' [...]');
                                        $content_en = Str::limit(strip_tags(html_entity_decode($priorityBlog->content_en)), 300, ' [...]');
                                    @endphp
                                        @if(app()->getLocale() == 'en')
                                            {!! $content_en !!}
                                        @elseif(app()->getLocale() == 'vi')
                                            {!! $content !!}
                                        @endif
                                        </div>
                                        <div class="meta-bot lh-1 mt-20">
                                            <ul class="d-flex">
                                                <li class="date me-5">
                                                    <a href="{{ route('client.blog.detail', ['slug' => $priorityBlog->slug]) }}">
                                                        <i class="la la-calendar me-2"></i>
                                                        
                                                        {{ date('Y-m-d H:i:s', strtotime($priorityBlog->created_at)) }}
                                                    </a>
                                                </li>
                                                {{-- <li class="author me-5">
                                                    <a href="#"><i class="la la-user me-2"></i> Tác giả: {{ $priorityBlog->user_name }}</a>
                                                </li> --}}
                                                {{-- <li class="comment">
                                                    <a href="#"><i class="la la-comment me-2"></i> 0 Bình luận</a>
                                                </li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="col-lg-8">
                                Bài viết ưu tiên ko có kết quả
                            </div>
                        @endif
                        <div class="col-lg-4 border-end brd-gray border-1">
                            <div class="tc-post-list-style2">
                                <div class="items">

                                @if(isset($blogWidgets) && is_object($blogWidgets))
                                    @foreach($blogWidgets as $value)
                                    <div class="item">
                                        <div class="row gx-3 align-items-center">
                                            <div class="col-4">
                                                <div class="img th-70 img-cover">
                                                    <a href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}">
                                                        <img src="{{ asset('upload/blog/'.$value->image_file) }}" alt="{{ $value->slug }}">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="content">
                                                    <div class="news-cat color-999 fsz-13px text-uppercase mb-1">
                                                        <a href="{{ route('client.blog.category', ['slug' => $value->slug]) }}" class="text-danger">
                                                            {{--  --}}
                                                            {{ app()->getLocale() === 'en' ? $value->category->name_en : $value->category_name }}
                                                        </a>
                                                    </div>
                                                    <h5 class="title ltspc--1">
                                                        <a style="font-size: 15px" href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}"
                                                           class="hover-underline">
                                                            @php
                                                                $title = Str::limit($value->title, 65, '...');
                                                            @endphp

                                                            {{ Str::limit(app()->getLocale() === 'en' ? $value->title_en : $value->title, 100, '...') }}
                                                            {{-- {!! $title !!} --}}
                                                        </a>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif


                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </section>
        <!-- ====== end Latest news ====== -->

        <!-- ====== start popular posts ====== -->
        <section class="tc-popular-posts-blog">
            <div class="container">
                <div class="content pt-20 pb-50 border-1 border-bottom brd-gray">
                    <p class="color-000 text-uppercase mb-30 ltspc-1"> <a href="#1">{{ __('messages.nhieuluotxem') }}</a> <i class="la la-angle-right ms-1"></i>

                    <div class="tc-post-grid-default">
                        <div class="tc-popular-posts-blog-slider9 tc-slider-style1">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">

                                @if(isset($blogByView) && is_object($blogByView))
                                    @foreach($blogByView as $value)
                                    <div class="swiper-slide">
                                        <div class="item">
                                            <div class="img img-cover th-180">
                                                <a href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}">
                                                    <img src="{{ asset('upload/blog/'. $value->image_file) }}" alt="{{  $value->slug }}">

                                                </a>
                                            </div>
                                            <div class="content pt-20">
                                                <a href="#"
                                                   class="news-cat color-999 fsz-13px text-uppercase mb-10">{{ app()->getLocale() === 'en' ? $value->category->name_en : $value->category_name }}</a>
                                                <h4 class="title ltspc--1 mb-10">
                                                    <a href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}">
                                                        {{ Str::limit(app()->getLocale() === 'en' ? $value->title_en : $value->title, 60, '...') }}
                                                    </a>
                                                </h4>
                                                <div class="text color-666">
                                                    @php
                                                        $content = Str::limit(strip_tags(html_entity_decode($value->content)), 90, ' [...]');
                                                        $content_en = Str::limit(strip_tags(html_entity_decode($value->content_en)), 90, ' [...]');
                                                    @endphp
                                                    <a href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}">
                                                        {{-- {{ $content }} --}}
                                                        @if(app()->getLocale() == 'en')
                                                            {!! $content_en !!}
                                                        @elseif(app()->getLocale() == 'vi')
                                                            {!! $content !!}
                                                        @endif
                                                    </a>

                                                </div>
                                                <div class="meta-bot lh-1 mt-20">
                                                    <ul class="d-flex">
                                                        <li class="date me-5">
                                                            <a href="#1"><i class="la la-calendar me-2"></i>
                                                                {{ date('Y-m-d H:i:s', strtotime($value->created_at)) }}
                                                            </a>
                                                        </li>
                                                        {{-- <li class="comment">
                                                            <a href="#1"><i class="la la-comment me-2"></i> 0</a>
                                                        </li> --}}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif

                                </div>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!-- ====== end popular posts ====== -->



    </main>
@endsection

@section('style')
        <link href='https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css' rel='stylesheet' />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #map {
            width: 100%;
            height: 450px;
        }
        .carousel-inner img {
            width: 100%;
            height: auto;
        }
    </style>
@endsection
