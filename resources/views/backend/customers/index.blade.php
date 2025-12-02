@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Khách hàng</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Danh sách khách hàng</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách khách hàng</h5>
                    <div class="ibox-tools">
                        @canModule('customers','create')
                            <a href="{{ route('admin.customers.create') }}" class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Thêm khách hàng
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
                                <th>Tên khách</th>
                                <th>SĐT</th>
                                <th>CMND/CCCD</th>
                                <th>Địa chỉ</th>
                                <th>Mã số thuế</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- $customers truyền từ CustomerController@index --}}
                            @forelse($customers as $index => $customer)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->id_card }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->tax_code }}</td>
                                    <td class="text-center">
                                        {{ optional($customer->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                            {{-- Xem chi tiết --}}
                                        @canModule('customers','read')
                                            <a href="{{ route('admin.customers.show', $customer->id) }}"
                                            class="btn btn-info btn-xs"
                                            title="Xem chi tiết khách hàng">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcanModule
                                        @canModule('customers','update')
                                            <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                               class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('customers','delete')
                                            <form action="{{ route('admin.customers.destroy', $customer->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa khách hàng này?');">
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
                                        Chưa có khách hàng nào.
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
