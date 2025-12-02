@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Hãng xe</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Danh sách hãng xe</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách hãng xe</h5>
                    <div class="ibox-tools">
                        @canModule('brands','create')
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Thêm hãng xe
                            </a>
                        @endcanModule
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Mã hãng</th>
                                <th>Tên hãng</th>
                                <th>Ghi chú</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($brands as $index => $brand)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $brand->code }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td>{{ $brand->note }}</td>
                                    <td class="text-center">
                                        {{ optional($brand->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        @canModule('brands','update')
                                            <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                               class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('brands','delete')
                                            <form action="{{ route('admin.brands.destroy', $brand->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa hãng xe này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcanModule
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Chưa có hãng xe nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection
@section('page-scripts')
<script>
$(function(){
    $('#checkAll').on('change', function(){
        $('.input-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('.dataTables').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        language: {
            search: "Tìm kiếm: ",
            lengthMenu: "Hiển thị _MENU_ mục",
            info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
            paginate: { previous: "Trước", next: "Tiếp" }
        },
        buttons: [
            { extend: 'copy'},
            { extend: 'csv'},
            { extend: 'excel', title: 'DongXe'},
            { extend: 'pdf', title: 'DongXe'},
            { extend: 'print',
              customize: function(win){
                  $(win.document.body).addClass('white-bg')
                      .css('font-size', '10px')
                      .find('table').addClass('compact').css('font-size', 'inherit');
              }
            }
        ]
    });
});
</script>
@endsection