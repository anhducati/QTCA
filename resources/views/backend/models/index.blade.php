@extends('layouts.panel')

@section('main')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Quản lý dòng xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Quản lý dòng xe</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách dòng xe</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.models.create') }}" class="btn btn-danger">
                            <i class="fa fa-plus"></i> Thêm dòng xe
                        </a>
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
                                    <th><input type="checkbox" id="checkAll" class="input-checkbox"></th>
                                    <th>Tên dòng xe</th>
                                    <th>Hãng xe</th>
                                    <th class="text-center">Ngày tạo</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($models as $model)
                                <tr class="gradeA">
                                    <td><input type="checkbox" class="input-checkbox"></td>
                                    <td>{{ $model->name }}</td>
                                    <td>{{ $model->brand->name ?? '' }}</td>
                                    <td class="text-center">
                                        @if($model->created_at)
                                            {{ $model->created_at->format('d-m-Y H:i') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.models.edit', $model->id) }}"
                                           class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                        <form action="{{ route('admin.models.destroy', $model->id) }}"
                                              method="POST" style="display:inline-block"
                                              onsubmit="return confirm('Xóa dòng xe này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
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
