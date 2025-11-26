@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.blog.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.blog.title') }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{config('apps.blog.heading') }}</h5>
                        <div class="ibox-tools">
                            <a href="{{ route('admin.blog.recycle') }}" class="btn btn-default"><i class="fa fa-trash"></i>  Thùng rác</a>
                            <a href="{{ route('admin.blog.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>  {{ config('apps.blog.createTitle') }} </a>
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>


                        </div>
                    </div>
                    <div class="ibox-content">
                            {{-- <form class="row justify-content-center"> <!-- Added justify-content-center for center alignment -->
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-1">
                                    <label for="username">Tác giả</label>
                                    <input type="text" name="username" id="username" value="{{ Request::get('username') }}" class="form-control">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-1">
                                    <label for="title">Tiêu đề</label>
                                    <input type="text" name="title" id="title" value="{{ Request::get('title') }}" class="form-control">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-1">
                                    <label for="category">Danh mục</label>
                                    <input type="text" name="category" id="category" value="{{ Request::get('category') }}" class="form-control">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-1">
                                    <label for="is_publish">Publish</label>
                                    <select name="is_publish" id="is_publish" class="form-control">
                                        <option value="">Select publish</option>
                                        <option {{ (Request::get('is_publish') == 1 ? 'selected' : '') }} value="1">Yes</option>
                                        <option {{ (Request::get('is_publish') == 100 ? 'selected' : '') }} value="100">No</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-1">
                                    <label for="start_date">Start date</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ Request::get('start_date') }}" class="form-control">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-1">
                                    <label for="end_date">End date</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ Request::get('end_date') }}" class="form-control">
                                </div>
                                <div class="col-12 text-center mt-2" style="padding-top: 10px;"> <!-- Áp dụng padding-top trực tiếp -->
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                                    <a href="{{ route('admin.blog') }}" class="btn btn-default"><i class="fa fa-recycle"></i> Đặt lại</a>
                                </div>
                                
                                
                            </form>
                         --}}
                        
{{-- 
                        <hr> --}}
                        <div class="table-responsive" >
                            @include('layouts.message')
                            <table class="table table-striped table-bordered table-hover dataTables">
                                <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>Tác giả</th>
                                    <th>Imgae</th>
                                    <th>Title</th>
                                    <th>View</th>
                                    <th>Danh mục</th>
                                    <th class="text-center">Publish</th>
                                    {{-- <th class="text-center">Status</th> --}}

                                    <th class="text-center">Create</th>
                                    <th class="text-center">Action</th>

                                </tr>
                                {{-- </thead> --}}
                                <tbody>
                                @if(isset($blogs) && is_object($blogs))
                                    @php
                                        $i = 0;
                                    @endphp

                                    @foreach($blogs as $value)
                                        @php
                                            $i++;
                                        @endphp
                                        <tr class="gradeA">
                                            <td>
                                                {{ $i }}
                                            </td>
                                            <td>{{ $value->user_name }}</td>
                                            <td>
                                                @if(isset($value->image_file))

                                                    <img src="{{ asset('upload/blog/' . $value->image_file) }}" alt="" class="img-fluid" style="width: 60px">

                                                @endif
                                            </td>
                                            <td>{{ $value->title }}</td>
                                            <td>{{ $value->view }}</td>
                                            <td>{{ $value->category_name }}</td>
                                            <td class="text-center">
                                                @if($value->is_publish == 1)
                                                    <a href="#"><i class="fa fa-check text-navy"></i></a>

                                                @else
                                                    <a href="#"><i class="fa fa-times text-danger"></i></a>
                                                @endif
                                            </td>
                                            {{-- <td class="text-center">
                                                @if($value->status == 1)
                                                    <span class="btn btn-sm btn-primary">Active</span>
                                                @else
                                                    <span class="btn btn-sm btn-warning">Inactive</span>
                                                @endif

                                            </td> --}}

                                            <td class="text-center">
                                                {{ date('d-m-Y H:i', strtotime($value->created_at)) }}
                                            </td>


                                            <td class="text-center" style="width: 100px">
                                                <a href="{{ route('admin.blog.update', $value->id) }}" class="btn btn-warning" type="button"><i class="fa fa-edit"></i></a>
                                                <a href="{{ route('admin.blog.delete', $value->id) }}" onclick="return confirm('Bạn có chắc muốn xóa?')"
                                                   class="btn btn-danger" type="button"><i class="fa fa-trash"></i></a>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                </tbody>
                                
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>





@endsection

@section('page-css')



@endsection

@section('page-scripts')
    <script>
        $(document).ready(function(){
            $('.dataTables').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                language: {
                    search: "Tìm kiếm: ",
                    lengthMenu: "Hiển thị _MENU_ mục",
                    info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                    paginate: {
                        previous: "Trước",
                        next: "Tiếp",
                    }
                },
                searching: true, // Ẩn ô tìm kiếm

                // Xuất file exel...
                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'FileEXCEL'},
                    {extend: 'pdf', title: 'FilePDF'},

                    {extend: 'print',
                        customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ]

            });

        });


    </script>

@endsection


