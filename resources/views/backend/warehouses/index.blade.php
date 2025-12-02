@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Danh sách kho</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách kho</h5>
                    <div class="ibox-tools">
                        @canModule('warehouses','create')
                            <a href="{{ route('admin.warehouses.create') }}" class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Thêm kho
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
                                <th>Mã kho</th>
                                <th>Tên kho</th>
                                <th>Địa chỉ</th>
                                <th>Ghi chú</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- $warehouses từ WarehouseController@index --}}
                            @forelse($warehouses as $index => $warehouse)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $warehouse->code }}</td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->address }}</td>
                                    <td>{{ $warehouse->note }}</td>
                                    <td class="text-center">
                                        {{ optional($warehouse->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        @canModule('warehouses','update')
                                            <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}"
                                               class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('warehouses','delete')
                                            <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa kho này?');">
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
                                    <td colspan="7" class="text-center text-muted">
                                        Chưa có kho nào.
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
