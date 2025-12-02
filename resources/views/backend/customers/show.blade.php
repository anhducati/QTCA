@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Chi tiết khách hàng</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.customers.index') }}">Khách hàng</a></li>
            <li class="active"><strong>{{ $customer->name }}</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Danh sách khách hàng
        </a>

        @canModule('customers','update')
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning">
                <i class="fa fa-edit"></i> Sửa khách hàng
            </a>
        @endcanModule
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    {{-- THÔNG BÁO --}}
    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')
        </div>
    </div>

    <div class="row">

        {{-- THÔNG TIN KHÁCH HÀNG --}}
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin khách hàng</h5>
                </div>
                <div class="ibox-content">

                    <table class="table table-borderless m-b-none">
                        <tr>
                            <th style="width:120px;">Tên khách</th>
                            <td><strong>{{ $customer->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>SĐT</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>CMND/CCCD</th>
                            <td>{{ $customer->id_card }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ</th>
                            <td>{{ $customer->address }}</td>
                        </tr>
                        <tr>
                            <th>Mã số thuế</th>
                            <td>{{ $customer->tax_code }}</td>
                        </tr>
                        <tr>
                            <th>Ghi chú</th>
                            <td>{{ $customer->note }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ optional($customer->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật</th>
                            <td>{{ optional($customer->updated_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

        {{-- LỊCH SỬ MUA XE / GIAO DỊCH --}}
{{-- ================== LỊCH SỬ MUA XE BÁN LẺ ================== --}}
<div class="col-lg-8">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Xe khách hàng đã mua</h5>
        </div>
        <div class="ibox-content">

            @if($sales->isEmpty())
                <p class="text-muted">Khách hàng chưa mua xe nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Ngày bán</th>
                            <th>Xe</th>
                            <th>Số khung</th>
                            <th>Giá bán</th>
                            <th>Thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.vehicle_sales.show', $sale->id) }}">
                                        <strong>{{ $sale->code }}</strong>
                                    </a>
                                </td>

                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>

                                <td>
                                    {{ optional(optional($sale->vehicle)->model)->name }}
                                    @if(optional($sale->vehicle->color ?? null)->name)
                                        ({{ $sale->vehicle->color->name }})
                                    @endif
                                </td>

                                <td>{{ optional($sale->vehicle)->frame_no }}</td>

                                <td>{{ number_format($sale->sale_price,0,',','.') }}</td>

                                <td>
                                    @if($sale->payment_status === 'paid')
                                        <span class="label label-success">Đã thanh toán</span>
                                    @elseif($sale->payment_status === 'partial')
                                        <span class="label label-warning">Còn nợ</span>
                                    @else
                                        <span class="label label-danger">Chưa thanh toán</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.vehicle_sales.show', $sale->id) }}"
                                       class="btn btn-info btn-xs">
                                        <i class="fa fa-eye"></i> Chi tiết
                                    </a>

                                    @if($sale->debt_amount > 0)
                                        <a href="{{ route('admin.vehicle_sales.payments.create', $sale->id) }}"
                                           class="btn btn-warning btn-xs">
                                            <i class="fa fa-money"></i> Thu nợ
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>



    </div>

</div>

@endsection
