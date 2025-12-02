@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Dòng xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Danh sách dòng xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách dòng xe</h5>
                    <div class="ibox-tools">
                        @canModule('models','create')
                            <a href="{{ route('admin.models.create') }}" class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Thêm dòng xe
                            </a>
                        @endcanModule
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Mã dòng</th>
                                <th>Tên dòng</th>
                                <th>Hãng xe</th>
                                <th>Năm</th>
                                <th>Ghi chú</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- $models được truyền từ VehicleModelController@index --}}
                            @forelse($models as $index => $model)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $model->code }}</td>
                                    <td>{{ $model->name }}</td>
                                    <td>{{ optional($model->brand)->name }}</td>
                                    <td>{{ $model->year }}</td>
                                    <td>{{ $model->note }}</td>
                                    <td class="text-center">
                                        {{ optional($model->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        @canModule('models','update')
                                            <a href="{{ route('admin.models.edit', $model->id) }}"
                                               class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('models','delete')
                                            <form action="{{ route('admin.models.destroy', $model->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa dòng xe này?');">
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
                                    <td colspan="8" class="text-center text-muted">
                                        Chưa có dòng xe nào.
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
