@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Quản lý hãng xe</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Quản lý hãng xe</strong>
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
                    <h5>Danh sách hãng xe</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-danger">
                            <i class="fa fa-plus"></i> Thêm hãng xe
                        </a>
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">

                    {{-- Thông báo --}}
                    @include('layouts.message')

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="checkAll" class="input-checkbox">
                                    </th>
                                    <th>Tên hãng</th>
                                    <th>Mô tả</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(isset($brands) && is_iterable($brands))
                                @foreach($brands as $brand)
                                    <tr class="gradeA">
                                        <td>
                                            <input type="checkbox" class="input-checkbox">
                                        </td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $brand->description }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                               class="btn btn-warning" type="button">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.brands.destroy', $brand->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa hãng xe này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div> {{-- .table-responsive --}}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-css')
    {{-- nếu cần CSS riêng cho DataTables thì thêm ở đây --}}
@endsection

@section('page-scripts')
<script>
    $(document).ready(function(){

        // Check all
        $('#checkAll').on('change', function () {
            var checked = $(this).is(':checked');
            $('.input-checkbox').prop('checked', checked);
        });

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
                { extend: 'csv'},
                { extend: 'excel', title: 'HangXe'},
                { extend: 'pdf', title: 'HangXe'},
                {
                    extend: 'print',
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
