@extends('layouts.client')

@section('main')
    <main>

        <!-- ====== start popular posts ====== -->
        <section class="tc-popular-posts-blog">
            <div class="container">
                
                <div class="content-widgets pt-3 pb-50">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="carrer-name-lg">{{ __('messages.nganhdaotao_') }}</h3>
                            <hr>
                            
                        </div>
                        <div class="col-lg-11">
                            <div class="tc-post-list-style3 mb-50">
                                
                                
                                <div class="items">

                                    @if(isset($majors) && is_object($majors))
                                        @foreach($majors as $value)
                                    <div class="item" style="padding-bottom: 0px"> 
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="img th-230 img-cover overflow-hidden">
                                                    <a href="{{ route('client.major.detail', $value->slug) }}">
                                                        {{-- <img class="image-blog" src="{{ asset('upload/major/'.$value->image_file) }}" alt="{{ $value->name }}" style="max-height: 180px; width: auto;"> --}}
                                                        @if(app()->getLocale() == 'en')
                                                        <img class="image-blog" src="{{ asset('upload/major/'.$value->image_file) }}" alt="{{ $value->name_en }}" style="max-height: 180px; width: auto;">
                                                        @elseif(app()->getLocale() == 'vi')
                                                        <img class="image-blog" src="{{ asset('upload/major/'.$value->image_file) }}" alt="{{ $value->name }}" style="max-height: 180px; width: auto;">
                                                        @endif
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="content mt-20 mt-lg-0">
                                                    {{-- <a href="#1"
                                                       class="color-999 fsz-13px text-uppercase mb-10">{{ $value->category_name }}</a> --}}
                                                    <h4 class="title mb-15">
                                                        <a href="{{ route('client.major.detail', $value->slug) }}">
                                                            {{-- {{ $value->name }} --}}
                                                            @if(app()->getLocale() == 'en')
                                                            {{ $value->name_en }}
                                                            @elseif(app()->getLocale() == 'vi')
                                                            {{ $value->name }}
                                                            @endif
                                                        </a>
                                                    </h4>
                                                    <div class="text color-666 mb-0">


                                                        @php
                                                             $content_en = Illuminate\Support\Str::limit(strip_tags(html_entity_decode($value->content_en)), 500, ' ...');
                                                            $content = Illuminate\Support\Str::limit(strip_tags(html_entity_decode($value->content)), 500, ' ...');
                                                            
                                                        @endphp

                                                        <a href="{{ route('client.major.detail', $value->slug) }}">
                                                            {{-- {!! $content !!} --}}
                                                            @if(app()->getLocale() == 'en')
                                                            {!! $content_en !!}
                                                            @elseif(app()->getLocale() == 'vi')
                                                            {!! $content !!}
                                                            @endif

                                                        </a>

                                                    </div>
                                                    <div class="meta-bot fsz-13px color-666 mb-4">
                                                        <ul class="d-flex">
                                                            <li class="date me-5">
                                                                <a href="#">
                                                                    <i class="la la-calendar me-2"></i>
                                                                    {{ date('d-m-Y H:i:s', strtotime($value->created_at)) }}

                                                                </a>
                                                            </li>
                                                            {{-- <li class="author me-5">
                                                                <a href="#"><i class="la la-user me-2"></i> Tác giả:
                                                                    <span class="color-000">
                                                                        {{ $value->user_name }}
                                                                    </span>
                                                                </a>
                                                            </li> --}}
                                                            
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

                            {{ $majors->links('vendor.pagination.custom') }}


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

    </style>
@endsection
