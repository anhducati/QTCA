@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.category.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.category.title') }}</strong>
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
                        <h5>{{config('apps.category.heading') }}</h5>
                        <div class="ibox-tools">
                            <a href="{{ route('admin.category.index') }}" class="btn btn-default"> <i class="fa fa-recycle"></i> Reset</a>
                            <a href="{{ route('admin.category.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>  Thêm danh mục </a>
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
                                    <th>Danh mục</th>
                                    <th>Danh mục En</th>
                                    {{-- <th>Slug</th> --}}
                                    <th>Meta Title</th>
                                    <th>Meta Description</th>
                                    <th>Meta keyword</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Create</th>
                                    <th class="text-center">Action</th>

                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($categories) && is_object($categories))
                                    @php
                                        $i = 0;
                                    @endphp

                                    @foreach($categories as $value)
                                        @php
                                            $i++;
                                        @endphp
                                        <tr class="gradeA">
                                            <td>{{ $i }} </td>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->name_en }}</td>
                                            {{-- <td>{{ $value->slug }}</td> --}}
                                            <td>{{ $value->meta_title }}</td>
                                            <td>{{ $value->meta_description }}</td>
                                            <td>{{ $value->meta_keyword }}</td>
                                            {{-- <td class="text-center">
                                                @if(!empty($value->status))

                                                    <span class="btn btn-sm btn-primary">Active</span>
                                                @else
                                                    <span class="btn btn-sm btn-warning">Inactive</span>
                                                @endif

                                            </td> --}}
                                            <td class="text-center">
                                                @if($value->is_menu === 1)
                                                    <span class="btn btn-sm btn-primary" style="min-width: 100px; text-align: center; display: inline-block;">Menu chính</span>
                                                @elseif($value->is_menu === 2)
                                                    <span class="btn btn-sm btn-info" style="min-width: 100px; text-align: center; display: inline-block;">Menu con</span>
                                                @else
                                                    <span class="btn btn-sm btn-warning" style="min-width: 100px; text-align: center; display: inline-block;">Không hiển thị</span>
                                                @endif
                                            </td>
                                            
                                            
                                            <td class="text-center">
                                                {{ date('d-m-Y H:i', strtotime($value->created_at)) }}
                                            </td>


                                            <td class="text-center">
                                                <a href="{{ route('admin.category.update', $value->id) }}" class="btn btn-warning" type="button"><i class="fa fa-edit"></i></a>
                                                {{-- <a href="{{ route('admin.category.delete', $value->id) }}" onclick="return confirm('Bạn có chắc muốn xóa?')"
                                                   class="btn btn-danger" type="button"><i class="fa fa-trash"></i></a> --}}
                                                    <a href="#" class="btn btn-danger" onclick="confirmDelete('{{ route('admin.category.delete', $value->id) }}')" type="button">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                
                                                
                                                <script>
                                                    function confirmDelete(deleteUrl) {
                                                        Swal.fire({
                                                            title: 'Bạn có chắc chắn muốn xóa?',
                                                            text: "Bạn sẽ không thể khôi phục lại mục này!",
                                                            icon: 'warning',
                                                            showCancelButton: true,
                                                            confirmButtonColor: '#3085d6',
                                                            cancelButtonColor: '#d33',
                                                            confirmButtonText: 'Vâng, xóa nó!',
                                                            cancelButtonText: 'Hủy bỏ'
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                window.location.href = deleteUrl;
                                                            }
                                                        });
                                                    }
                                                </script>
                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                </tbody>
                                {{-- <tfoot>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Meta Title</th>
                                    <th>Meta Description</th>
                                    <th>Meta keyword</th>
                                    <th>Status</th>
                                    <th>Create</th>
                                    <th>Action</th>
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


