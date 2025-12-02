@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Nhà cung cấp</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Danh sách nhà cung cấp</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách nhà cung cấp</h5>
                    <div class="ibox-tools">
                        @canModule('suppliers','create')
                            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Thêm nhà cung cấp
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
                                <th>Tên NCC</th>
                                <th>SĐT</th>
                                <th>Email</th>
                                <th>Địa chỉ</th>
                                <th>Mã số thuế</th>
                                <th>Ghi chú</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- $suppliers từ SupplierController@index --}}
                            @forelse($suppliers as $index => $supplier)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->address }}</td>
                                    <td>{{ $supplier->tax_code }}</td>
                                    <td>{{ $supplier->note }}</td>
                                    <td class="text-center">
                                        @canModule('suppliers','update')
                                            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}"
                                               class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('suppliers','delete')
                                            <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?');">
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
                                        Chưa có nhà cung cấp nào.
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
