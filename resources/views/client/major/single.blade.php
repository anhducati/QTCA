@extends('layouts.client')

@section('main')
    <main>


<section class="tc-latest-news-style1">
            <div class="container">
                <div class="section-content pt-30 pb-50 border-bottom border-1 brd-gray">   
                    <div class="row mb-4">
                        <div class="col-12 breadcrumb">
                            <a href="{{ route('client.major') }}">{{ __('messages.nganhdaotao') }} </a> 
                            @if(app()->getLocale() == 'en')
                            <span  class="carrer-name ml-1" style="font-weight: 500" href=""> {{ $major->name_en }}</span>
                            @elseif(app()->getLocale() == 'vi')
                            <span  class="carrer-name ml-1" style="font-weight: 500" href=""> {{ $major->name }}</span>
                            @endif
                           
                        </div>

                        <div class="col-12">
                       {{-- <h3 class="carrer-name-lg">NGÀNH ĐÀO TẠO</h3> --}}
                            <hr> 
                            @if(app()->getLocale() == 'en')
                            <p class="carrer-name-sm">{{ $major->name_en }}</p>
                            @elseif(app()->getLocale() == 'vi')
                            <p class="carrer-name-sm">{{ $major->name }}</p>
                            @endif

                            <b>{{ __('messages.ngaydang') }}{{ $major->created_at }}</b>
                        </div>

                        <div class="col-12 mt-3"> 
                            <p>
                                <span class="carrer-name">{{ __('messages.manganh') }}</span>
                                @if(app()->getLocale() == 'en')
                                <span class="carrer-name-2">{{ $major->name_en }}</span>
                                @elseif(app()->getLocale() == 'vi')
                                <span class="carrer-name-2">{{ $major->name }}</span>
                                @endif
                            </p>
                            
                            <p>
                                <span class="carrer-name">{{ __('messages.manganh') }}</span>
                                <span class="carrer-name-3">{{ $major->major_code }}</span>
                            </p>

                            <p>
                                <span class="carrer-name">{{ __('messages.tohopXT') }}</span>
                                
                                <span class="carrer-name-4">
                                    @if(isset($subjects) && is_object($subjects))
                                        @php
                                            $comma = ', ';
                                        @endphp
                                        @foreach($subjects as $value)
                                        @php
                                            $examBlock = \App\Models\ExamBlock::find($value->exam_block_id);
                                        @endphp
                                           
                                            @if(app()->getLocale() == 'en')
                                            @if($loop->last)
                                            <span style="font-weight: 500" class="carrer-name-4">{{ $examBlock->name_en }}</span>
                                            @else
                                                <span style="font-weight: 500" class="carrer-name-4">{{ $examBlock->name_en }}{{ $comma }}</span>
                                            @endif
                                            @elseif(app()->getLocale() == 'vi')
                                            @if($loop->last)
                                                <span style="font-weight: 500" class="carrer-name-4">{{ $examBlock->name }}</span>
                                            @else
                                                <span style="font-weight: 500" class="carrer-name-4">{{ $examBlock->name }}{{ $comma }}</span>
                                            @endif
                                            @endif
                                        @endforeach
                                    @endif
                                
                                </span>
                                   
                            </p>
                            
                        </div>

                        <div class="col-12 mt-3">
                            
                            
                            @if(app()->getLocale() == 'en')
                            {!! $major->content_en !!}
                            @elseif(app()->getLocale() == 'vi')
                            {!! $major->content !!}
                            @endif


                        </div>

                    </div>

                    

                    

                    
                    

                </div>
            </div>
        </section>

    </main>
@endsection