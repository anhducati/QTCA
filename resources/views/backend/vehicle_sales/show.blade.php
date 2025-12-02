@extends('layouts.panel')

@section('main')

@php
    $vehicle = $sale->vehicle;
    $customer = $sale->customer;
    $model = optional($vehicle->model);
    $brand = optional($model->brand);
    $color = optional($vehicle->color);

    $amount = $sale->amount ?? 0;
    $paid   = $sale->paid_amount ?? 0;
    $debt   = $sale->debt_amount ?? max($amount - $paid, 0);
@endphp

<div class="row wrapper border-bottom white-bg page-heading no-print">
    <div class="col-lg-10">
        <h2>Hóa đơn bán lẻ</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.vehicle_sales.index') }}">Bán lẻ xe</a></li>
            <li class="active"><strong>{{ $sale->code }}</strong></li>
        </ol>
    </div>
    <div class="col-lg-2 text-right m-t-lg">
        <a href="javascript:window.print();" class="btn btn-primary">
            <i class="fa fa-print"></i> In hóa đơn
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox">
                <div class="ibox-content">

                    {{-- PHẦN HEADER HÓA ĐƠN --}}
                    <div class="row">
                        <div class="col-xs-6">
                            <h3><strong>CỬA HÀNG XE MÁY / XE ĐIỆN CHUNG ANH</strong></h3>
                            <p>
                                Địa chỉ: Yên Khoái - Nga Sơn - Thanh Hóa<br>
                                Hotline: 02373.872.666<br>
                            </p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <h2><strong>HÓA ĐƠN BÁN LẺ</strong></h2>
                            <p>
                                Số: <strong>{{ $sale->code }}</strong><br>
                                Ngày: <strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</strong><br>
                            </p>
                        </div>
                    </div>

                    <hr>

                    {{-- THÔNG TIN KHÁCH HÀNG --}}
                    <div class="row m-b-sm">
                        <div class="col-xs-6">
                            <h4>Khách hàng</h4>
                            <p>
                                Họ tên: <strong>{{ $customer->name ?? '-' }}</strong><br>
                                SĐT: <strong>{{ $customer->phone ?? '-' }}</strong><br>
                                Địa chỉ: {{ $customer->address ?? '-' }}<br>
                            </p>
                        </div>
                        <div class="col-xs-6">
                            <h4>Thông tin thanh toán</h4>
                            <p>
                                Hình thức: {{ $sale->payment_method ?? 'Không rõ' }}<br>
                                Nhân viên lập: {{ optional($sale->createdBy)->name ?? '-' }}<br>
                            </p>
                        </div>
                    </div>

                    {{-- THÔNG TIN XE --}}
                    <h4>Chi tiết xe</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Hãng</th>
                                    <th>Dòng xe</th>
                                    <th>Màu</th>
                                    <th>Số khung</th>
                                    <th>Số máy</th>
                                    <th>Biển số</th>
                                    <th class="text-right">Giá bán</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $brand->name ?? '-' }}</td>
                                    <td>{{ $model->name ?? '-' }}</td>
                                    <td>{{ $color->name ?? '-' }}</td>
                                    <td>{{ $vehicle->frame_no ?? '-' }}</td>
                                    <td>{{ $vehicle->engine_no ?? '-' }}</td>
                                    <td>{{ $vehicle->license_plate ?? '-' }}</td>
                                    <td class="text-right">
                                        {{ number_format($sale->sale_price ?? 0, 0, ',', '.') }}
                                    </td>
                                   
                                    <td class="text-right">
                                        {{ number_format($amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- TỔNG TIỀN --}}
                    <div class="row m-t-sm">
                        <div class="col-xs-6">
                            <p><strong>Bằng chữ:</strong>
                                {{-- Anh có thể bổ sung hàm đổi số thành chữ VNĐ phía backend --}}
                                ............................................................
                            </p>
                            <p>Ghi chú: {{ $sale->note ?? 'Không có' }}</p>
                        </div>
                        <div class="col-xs-6 text-right">
                            <p>
                                Tổng tiền: 
                                <strong>{{ number_format($amount, 0, ',', '.') }}</strong> VNĐ
                            </p>
                            <p>
                                Khách trả:
                                <strong>{{ number_format($paid, 0, ',', '.') }}</strong> VNĐ
                            </p>
                            <p>
                                Còn nợ:
                                <strong>{{ number_format($debt, 0, ',', '.') }}</strong> VNĐ
                            </p>
                        </div>
                    </div>

                    <hr>

                    {{-- CHỮ KÝ --}}
                    <div class="row text-center m-t-lg">
                        <div class="col-xs-4">
                            <strong>Người mua hàng</strong><br>
                            <em>(Ký, ghi rõ họ tên)</em>
                            <br><br><br><br>
                        </div>
                        <div class="col-xs-4">
                            <strong>Người nhận tiền</strong><br>
                            <em>(Ký, ghi rõ họ tên)</em>
                            <br><br><br><br>
                        </div>
                        <div class="col-xs-4">
                            <strong>Người lập hóa đơn</strong><br>
                            <em>(Ký, ghi rõ họ tên)</em>
                            <br><br><br><br>
                        </div>
                    </div>

                    <div class="no-print text-right m-t-md">
                        <a href="{{ route('admin.vehicle_sales.create') }}" class="btn btn-default">
                            <i class="fa fa-plus"></i> Bán tiếp xe khác
                        </a>
                        <a href="{{ route('admin.vehicle_sales.print', $sale->id) }}"
                                    target="_blank"
                                    class="btn btn-primary">
                                    <i class="fa fa-print"></i> In hợp đồng
                                    </a>

                        <a href="javascript:window.print();" class="btn btn-primary">
                            <i class="fa fa-print"></i> In hóa đơn
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: #fff;
    }
}
</style>

@endsection
