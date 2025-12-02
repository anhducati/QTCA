@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Chi tiết xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.vehicles.index') }}">Danh sách xe</a></li>
            <li class="active">
                <strong>{{ $vehicle->frame_no }}</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>

        @canModule('vehicles', 'update')
            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-warning">
                <i class="fa fa-edit"></i> Sửa
            </a>
        @endcanModule
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        {{-- THÔNG TIN CHUNG --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin xe</h5>
                </div>
                <div class="ibox-content">

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Hãng xe:</label>
                            <p><strong>{{ optional($vehicle->brand)->name ?? optional($vehicle->model->brand ?? null)->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Dòng xe:</label>
                            <p><strong>{{ optional($vehicle->model)->name }}</strong></p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Màu sắc:</label>
                            <p>{{ optional($vehicle->color)->name ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>Năm sản xuất:</label>
                            <p>{{ $vehicle->year ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Số khung (VIN):</label>
                            <p><strong>{{ $vehicle->frame_no }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Số máy:</label>
                            <p>{{ $vehicle->engine_no ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Kho hiện tại:</label>
                            <p>{{ optional($vehicle->warehouse)->name ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>Trạng thái:</label>
                            <p>
                                @php $st = $vehicle->status; @endphp
                                @if($st == 0 || $st == '0' || $st == 'in_stock')
                                    <span class="label label-primary">Trong kho</span>
                                @elseif($st == 2 || $st == '2')
                                    <span class="label label-warning">Đặt cọc</span>
                                @elseif($st == 1 || $st == '1' || $st == 'sold')
                                    <span class="label label-success">Đã bán</span>
                                @elseif($st == 'demo')
                                    <span class="label label-info">Demo / sự kiện</span>
                                @else
                                    <span class="label label-default">{{ $st }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Biển số:</label>
                            <p>{{ $vehicle->license_plate ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>Giá nhập:</label>
                            <p>
                                @if($vehicle->purchase_price)
                                    {{ number_format($vehicle->purchase_price, 0, ',', '.') }} VNĐ
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Nhà cung cấp:</label>
                            <p>{{ optional($vehicle->supplier)->name ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>Ngày tạo:</label>
                            <p>{{ optional($vehicle->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-12">
                            <label>Ghi chú:</label>
                            <p>{{ $vehicle->note ?: 'Không có' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- LỊCH SỬ NHẬP / BÁN / XUẤT --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Tình trạng sử dụng xe</h5>
                </div>
                <div class="ibox-content">

                    {{-- 1. NHẬP KHO --}}
                    <h5>1. Nhập kho</h5>
                    @if($vehicle->importReceipt)
                        <p>
                            Phiếu nhập:
                            <a href="{{ route('admin.import_receipts.show', $vehicle->importReceipt->id) }}">
                                {{ $vehicle->importReceipt->code }}
                            </a><br>
                            Ngày nhập:
                            {{ $vehicle->importReceipt->import_date
                                ? \Carbon\Carbon::parse($vehicle->importReceipt->import_date)->format('d/m/Y')
                                : '-' }}<br>
                            Nhà cung cấp: {{ optional($vehicle->importReceipt->supplier)->name ?: '-' }}<br>
                            Kho nhập: {{ optional($vehicle->importReceipt->warehouse)->name ?: '-' }}
                        </p>
                    @else
                        <p class="text-muted">Xe không gắn với phiếu nhập nào.</p>
                    @endif

                    <hr>

                   @php
    // Ưu tiên hóa đơn bán lẻ (VehicleSale)
    $retailSale = $vehicle->retailSale ?? null;
                @endphp

                {{-- ===== 2. BÁN LẺ (NẾU CÓ VehicleSale) ===== --}}
                @if($retailSale)
                    <h5>2. Bán lẻ</h5>
                    <p>
                        Mã HĐ:
                        <a href="{{ route('admin.vehicle_sales.show', $retailSale->id) }}">
                            {{ $retailSale->code }}
                        </a><br>

                        Ngày bán:
                        {{ $retailSale->sale_date
                            ? \Carbon\Carbon::parse($retailSale->sale_date)->format('d/m/Y')
                            : '-' }}<br>

                        Khách hàng:
                        {{ optional($retailSale->customer)->name ?: '-' }}
                        @if(optional($retailSale->customer)->phone)
                            ({{ $retailSale->customer->phone }})
                        @endif
                        <br>

                        Giá bán:
                        @if($retailSale->sale_price)
                            {{ number_format($retailSale->sale_price, 0, ',', '.') }} VNĐ
                        @else
                            -
                        @endif
                        <br>

                        Trạng thái thanh toán:
                        @if($retailSale->payment_status === 'paid')
                            <span class="label label-success">Đã thanh toán</span>
                        @elseif($retailSale->payment_status === 'partial')
                            <span class="label label-warning">Còn nợ</span>
                        @elseif($retailSale->payment_status === 'unpaid')
                            <span class="label label-danger">Chưa thanh toán</span>
                        @else
                            <span class="label label-default">{{ $retailSale->payment_status }}</span>
                        @endif
                    </p>

                {{-- ===== 2. XUẤT KHO / BÁN BUÔN (CHỈ KHI KHÔNG CÓ BÁN LẺ) ===== --}}
                @elseif(!empty($lastExport))
                    <h5>2. Xuất kho / Bán buôn</h5>
                    <p>
                        Phiếu xuất gần nhất:
                        <a href="{{ route('admin.export_receipts.show', $lastExport->exportReceipt->id) }}">
                            {{ $lastExport->exportReceipt->code }}
                        </a><br>

                        Ngày xuất:
                        {{ $lastExport->exportReceipt->export_date
                            ? \Carbon\Carbon::parse($lastExport->exportReceipt->export_date)->format('d/m/Y')
                            : '-' }}<br>

                        Loại xuất:
                        @php $t = $lastExport->exportReceipt->export_type; @endphp
                        @if($t === 'sell')
                            Bán buôn
                        @elseif($t === 'transfer')
                            Chuyển kho
                        @elseif($t === 'demo')
                            Xe demo / sự kiện
                        @else
                            {{ $t }}
                        @endif
                        <br>

                        Đối tác nhận xe:
                        {{ optional($lastExport->exportReceipt->supplier)->name ?: '-' }}
                    </p>

                {{-- ===== CHƯA BÁN LẺ & CHƯA XUẤT KHO ===== --}}
                @else
                    <h5>2. Bán lẻ</h5>
                    <p class="text-muted">Chưa có thông tin bán lẻ.</p>

                    <hr>

                    <h5>3. Xuất kho / Bán buôn</h5>
                    <p class="text-muted">Chưa có phiếu xuất kho nào gắn với xe này.</p>
                @endif


                </div>
            </div>
        </div>

    </div>
</div>

@endsection
