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
                        <h5>Thùng rác bài viết</h5>
                        <div class="ibox-tools">
                            <a href="{{ route('admin.blog') }}" class="btn btn-default"><i class="fa fa-list"></i>  Danh sách bài viết </a>
                            <a href="{{ route('admin.blog.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>  {{ config('apps.blog.createTitle') }} </a>
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>


                        </div>
                    </div>
                    <div class="ibox-content">


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
                                    <th>Danh mục</th>
                                    <th class="text-center">Publish</th>
                                    <th class="text-center">Status</th>

                                    <th class="text-center">Create</th>
                                    <th class="text-center">Action</th>

                                </tr>
                                </thead>
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
                                            <td>{{ $value->category_name }}</td>
                                            <td class="text-center">
                                                @if($value->is_publish == 1)
                                                    <a href="#"><i class="fa fa-check text-navy"></i></a>

                                                @else
                                                    <a href="#"><i class="fa fa-times text-danger"></i></a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($value->status == 1)
                                                    <span class="btn btn-sm btn-primary">Active</span>
                                                @else
                                                    <span class="btn btn-sm btn-warning">Inactive</span>
                                                @endif

                                            </td>

                                            <td class="text-center">
                                                {{ date('d-m-Y H:i', strtotime($value->created_at)) }}
                                            </td>


                                            <td class="text-center" style="width: 100px">
                                                <a href="{{ route('admin.blog.restore', $value->id) }}"  onclick="return confirm('Bạn có chắc chắn muốn khôi phục bài viết?')"
                                                   class="btn btn-warning" type="button"><i class="fa fa-window-restore"></i>

                                                </a>
                                                <a href="{{ route('admin.blog.force-delete', $value->id) }}" onclick="return confirm('Bạn có chắc muốn xóa?')"
                                                   class="btn btn-danger" type="button"><i class="fa fa-trash"></i>

                                                </a>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Title</th>


                                    <th>Category</th>
                                    <th class="text-center">Publish</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Create</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </tfoot>
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



