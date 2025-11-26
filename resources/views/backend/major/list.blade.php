@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.major.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.major.title') }}</strong>
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
                        <h5>{{config('apps.major.heading') }}</h5>
                        <div class="ibox-tools">
                            <a href="{{ route('admin.major') }}" class="btn btn-default"> <i class="fa fa-recycle"></i> Reset</a>
                            <a href="{{ route('admin.major.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>  Thêm ngành đào tạo </a>
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
                                    <th>Tên ngành</th>
                                    <th>Tên ngành EN</th>
                                    <th>Hình ảnh</th>
                                    <th>Mã ngành</th>
                                    
                                    <th>Ngày tạo</th>
                                    
                                    <th class="text-center">Thao tác</th>

                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($listMajors) && is_object($listMajors))
                                    @php
                                        $i = 0;
                                    @endphp

                                    @foreach($listMajors as $value)
                                        @php
                                            $i++;
                                        @endphp
                                        <tr class="gradeA">
                                            <td>
                                                {{ $i }}
                                            </td>
                                            <td>
                                                {{ $value->name }}
                                            </td>
                                            <td>
                                                {{ $value->name_en }}
                                            </td>

                                            <td>
                                                @if(isset($value->image_file))

                                                    <img src="{{ asset('upload/major/' . $value->image_file) }}" alt="" class="img-fluid" style="width: 60px">

                                                @endif
                                            </td>

                                            <td>{{ $value->major_code }}</td>
             
                                            <td >
                                                {{ date('d-m-Y H:i', strtotime($value->created_at)) }}
                                            </td>


                                            <td class="text-center">
                                                <a href="{{ route('admin.major.update', $value->id) }}" class="btn btn-warning" ><i class="fa fa-edit"></i></a>
                                                <a href="{{ route('admin.major.delete', $value->id) }}" onclick="return confirm('Bạn có chắc muốn xóa?')"
                                                   class="btn btn-danger" type="button"><i class="fa fa-trash"></i></a>

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
                                    <th>Tên ngành</th>
                                    <th>Hình ảnh</th>

                                    <th>Mã ngành</th>
                                    <th>Ngày tạo</th>
                                    
                                    <th>Thao tác</th>
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


