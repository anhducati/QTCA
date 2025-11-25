@extends('layouts.client')

@section('main')

    <div class="tc-single-post-features-page">



        <main>

            <!-- ====== start features posts ====== -->
            <section class="tc-main-post-style2 pt-50 pb-100">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-9 pe-0 pe-lg-5">
                            <div class="main-content-side">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li style="margin-right: 8px">
                                            <a href="{{ route('client.home') }}">{{ __('messages.home') }}</a> /
                                        </li>
                                        <li style="margin-right: 8px">
                                            <a href="#">{{ __('messages.baiviet') }}</a> /
                                        </li>
                                        <li style="margin-right: 8px">
                                            {{-- <a href="{{ route('client.blog.category', ['slug' => $blog->category_slug] ) }}">{{ $blog->category_name}} </a> / --}}
                                            @if(app()->getLocale() == 'en')
                                            <a href="{{ route('client.blog.category', ['slug' => $blog->category_slug] ) }}">{{ $blog->category_nameEN }} </a> /
                                            @elseif(app()->getLocale() == 'vi')
                                            <a href="{{ route('client.blog.category', ['slug' => $blog->category_slug] ) }}">{{ $blog->category_name}} </a> /
                                            @endif
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            {{-- {{ $blog->title }} --}}
                                            @if(app()->getLocale() == 'en')
                                            {{ $blog->title_en }}
                                            @elseif(app()->getLocale() == 'vi')
                                            {{ $blog->title }}
                                            @endif
                                        </li>
                                    </ol>
                                </nav>
                                <!-- ====== start audio ====== -->
{{--                                <div class="audio-content mt-40">--}}
{{--                                    <audio controls class="audio">--}}
{{--                                        <source src="{{ asset('upload/blog/'.$blog->image_file) }}" type="audio/mpeg">--}}
{{--                                    </audio>--}}
{{--                                    <span class="title">Listen to this article!</span>--}}
{{--                                </div>--}}
                                <h4 class="sub-title mt-40">
                                    @if(app()->getLocale() == 'en')
                                    {{ $blog->title_en }}
                                    @elseif(app()->getLocale() == 'vi')
                                    {{ $blog->title }}
                                    @endif

                                </h4>
                                <!-- ====== start images ====== -->
                                <div class="images-by mt-40">
                                    <div class="row gx-2">
                                        <div class="col-lg-12">
                                            <div class="img img-cover th-280 mt-15">
                                                <img style="height: 100%" src="{{ asset('upload/blog/'.$blog->image_file) }}" alt="">
                                            </div>
                                            <div class="text-center color-999 py-3 fst-italic">

                                                    {{-- <span>{{ $blog->meta_keyword }}</span> --}}
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <p class="info-text mt-40">
                                    <span class="lg-letter">

                                    </span>
                                    {{-- {!! $blog->content !!} --}}
                                    @if(app()->getLocale() == 'en')
                                    {!! $blog->content_en !!}
                                    @elseif(app()->getLocale() == 'vi')
                                    {!! $blog->content !!}
                                    @endif
                                </p>



                                <!-- ====== start post slider ====== -->


                                <!-- ====== start sharing ====== -->
                                {{-- <div class="btm-share-post mt-50">
                                    <div class="row items">
                                        <div class="col-lg-6">
                                            <div class="btm-tags mb-4 mb-lg-0">
                                                @if(isset($blogTags) && is_object($blogTags))
                                                    @foreach($blogTags as $value)
                                                    <a href="{{ route('client.blog.tag', ['name' => $value->name ]) }}">{{ $value->name }}</a>

                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="btm-sharing d-lg-flex align-items-lg-center justify-content-lg-end">
                                                <p class="text-capitalize me-20 mb-2 mb-lg-0">Chia sẽ</p>
                                                <div class="share-icons">
                                                    <a href="#"> <i class="la la-twitter"></i> </a>
                                                    <a href="#"> <i class="la la-facebook-f"></i> </a>
                                                    <a href="#"> <i class="la la-instagram"></i> </a>
                                                    <a href="#"> <i class="la la-youtube"></i> </a>
                                                    <a href="#"> <i class="la la-spotify"></i> </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <!-- ====== start author-info ====== -->
                                {{-- <div class="tc-author-info-style1">
                                    <div class="tc-author-card">
                                        <div class="content mt-50 p-40 d-block d-lg-flex bg-gray1">
                                            <div class="img img-cover icon-85 rounded-circle overflow-hidden flex-shrink-0 me-30">
                                               @if(!empty($blog->image))
                                                    <img src="{{ $blog->image }}" alt="">
                                                @else
                                                    <img src="{{ asset('upload/user-default.png') }}" alt="">
                                               @endif
                                            </div>
                                            <div class="info">
                                                <h5 class="title fsz-24px fw-bold">{{ $blog->user_name }}</h5>
                                                <small class="fsz-12px color-main text-uppercase">Tác giả</small>
                                                <div class="text fsz-15px color-666 mt-20">

                                                </div>
                                                <div class="social-links mt-20 fsz-19px">
                                                    <a href="#" class="me-15"><i class="la la-twitter"></i></a>
                                                    <a href="#" class="me-15"><i class="la la-facebook-f"></i></a>
                                                    <a href="#" class="me-15"><i class="la la-instagram"></i></a>
                                                    <a href="#"><i class="la la-youtube"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- ====== Bình Luận Facebook ====== -->
                                <!-- ====== start comment facebook ====== -->
                                {{-- <div class="tc-author-info-style1">
                                    @php
                                        $current_url = Request::url();
                                    @endphp
                                    <div class="fb-comments" data-href="{{ $current_url }}" data-width="100%" data-numposts="10"></div>
                                </div> --}}
                                <!-- ====== end comment facebook ====== -->


                                <!-- ====== start slider ====== -->
                                <div class="tc-next-prev-post mb-60 mt-60 pt-60 border-1 border-top border-dark">
                                    <div class="tc-next-prev-post-slider">
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                @if(isset($listBlogs) && is_object($listBlogs))
                                                    @foreach($listBlogs as $value)
                                                        <div class="swiper-slide">
                                                            <a href="{{ route('client.blog.detail', ['slug' => $value->slug]) }}" class="item">
                                                                
                                                                <h6 class="title">
                                                                    {{-- {{    Str::limit($value->title, 50, '...') }} --}}
                                                                    @if(app()->getLocale() == 'en')
                                                                    {{    Str::limit($value->title_en, 50, '...') }}
                                                                    @elseif(app()->getLocale() == 'vi')
                                                                    {{    Str::limit($value->title, 50, '...') }}
                                                                    @endif
                                                                </h6>
                                                            </a>
                                                        </div>
                                                     @endforeach
                                                @endif

                                                
                                            </div>
                                        </div>
                                        <div class="swiper-button-next"></div>
                                        <div class="swiper-button-prev"></div>
                                    </div>
                                </div>

                                <!-- ======  comments  custom====== -->
                                <!-- ====== start comments ====== -->

                            </div>
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
                                            <a href="{{ route('client.blog.category', ['slug' => $value->slug] ) }}">{{ app()->getLocale() === 'en' ? $value->name_en : $value->name }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!-- end Danh mục -->
                                

                                <div class="tc-post-list-style2 pb-40">
                                    <p class="color-000 text-uppercase mb-30">{{ __('messages.baivietmoi') }} </p>
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
                                                                {{-- @php
                                                                    // $title = Str::limit($value->title, 40, '...');
                                                                   
                                                                @endphp --}}

                                                                @if(app()->getLocale() == 'en')
                                                                {{    Str::limit($value->title_en, 40, '...') }}
                                                                @elseif(app()->getLocale() == 'vi')
                                                                {{    Str::limit($value->title, 40, '...') }}
                                                                @endif

                                                                {{-- {!! $title !!} --}}
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
            </section>
            <!-- ====== end features posts ====== -->


            <!-- ====== start popular posts ====== -->

            <!-- ====== end popular posts ====== -->

            <!-- ====== start modals ====== -->

             <div class="offcanvas offcanvas-start sidebar-popup-style1" tabindex="-1" id="offcanvasExample"
             aria-labelledby="offcanvasExampleLabel">
            {{-- <div class="offcanvas-header">
                <div class="logo">
                    <img src="{{ asset('assets/client/img/logo_home1.png') }}" alt="" class="dark-none">
                    <img src="{{ asset('assets/client/img/logo_home1_lt.png') }}" alt="" class="light-none">
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div> --}}
            {{-- <div class="offcanvas-body mt-4">
                
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
                
            </div> --}}
        </div>
            <!-- ====== end modals ====== -->

        </main>
    </div>
@endsection
