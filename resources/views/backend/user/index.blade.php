@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{config('apps.user.title') }}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>

            <li class="active">
                <strong>{{config('apps.user.title') }}</strong>
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
                    <h5>{{config('apps.user.heading') }}</h5>
                    <div class="ibox-tools">
                        {{-- <a href="{{ route('admin.user.create') }}" class="btn btn-default"><i class="fa fa-trash"></i>  Thùng rác</a> --}}
                        <a href="{{ route('admin.user.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>  Thêm thành viên</a>
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
                                        <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                                    </th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th class="text-center">Xác thực</th>
                                    <th class="text-center">Ngày tạo</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(isset($users) && is_object($users))
                                @foreach($users as $user)
                                <tr class="gradeA">
                                    <td>
                                        <input type="checkbox" value="" class="input-checkbox">
                                    </td>
                                    <td>
                                        {{ $user->name }}
                                    </td>

                                    <td>{{ $user->email }}</td>

                                    <td class="text-center">
                                        @if(!empty($user->email_verified_at))
                                            <a href="#"><i class="fa fa-check text-navy"></i></a>
                                        @else
                                            <a href="#"><i class="fa fa-times text-danger"></i></a>
                                        @endif

{{--                                        {{ !empty($user->email_verified_at) ? 'Yes' : 'No' }}--}}
                                    </td>

                                    <td class="text-center">
                                        {{ date('d-m-Y H:i', strtotime($user->created_at)) }}
                                    </td>

                                    <td class="text-center">
                                        @if(!empty($user->status))
                                            <span class="btn btn-sm btn-primary">Active</span>
                                        @else
                                            <span class="btn btn-sm btn-warning">Inactive</span>
                                        @endif

                                    </td>

{{--                                    <td class="text-center">--}}
{{--                                        <input type="checkbox" class="js-switch" checked />--}}
{{--                                    </td>--}}
                                    <td class="text-center">
                                        <a href="{{ route('admin.user.update', $user->id) }}" class="btn btn-warning" type="button"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('admin.user.delete', $user->id) }}" onclick="return confirm('Bạn có chắc muốn xóa')"
                                           class="btn btn-danger" type="button"><i class="fa fa-trash"></i></a>

                                    </td>
                                </tr>
                                @endforeach
                            @endif


                            </tbody>
                            {{-- <tfoot>
                            <tr>
                                <th>
                                    <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                                </th>
                                <th>Avatar</th>
                                <th>Họ tên</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </tfoot> --}}
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

