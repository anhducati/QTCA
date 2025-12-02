@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Danh sách hóa đơn bán lẻ</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Bán lẻ xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        @canModule('vehicle_sales', 'create')
            <a href="{{ route('admin.vehicle_sales.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Tạo hóa đơn bán lẻ
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

    {{-- BỘ LỌC --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox collapsed">
                <div class="ibox-title">
                    <h5>Bộ lọc</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" style="display:none;">
                    <form method="GET" action="{{ route('admin.vehicle_sales.index') }}" class="form-inline">

                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Mã HĐ</label>
                            <input type="text" name="code"
                                   value="{{ request('code') }}"
                                   class="form-control input-sm"
                                   placeholder="VD: HDBL_1">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Từ ngày</label>
                            <input type="date" name="from"
                                   value="{{ request('from') }}"
                                   class="form-control input-sm">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Đến ngày</label>
                            <input type="date" name="to"
                                   value="{{ request('to') }}"
                                   class="form-control input-sm">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">SĐT KH</label>
                            <input type="text" name="phone"
                                   value="{{ request('phone') }}"
                                   class="form-control input-sm"
                                   placeholder="09xx...">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Trạng thái</label>
                            <select name="payment_status" class="form-control input-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="unpaid"  {{ request('payment_status') === 'unpaid'  ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Còn nợ</option>
                                <option value="paid"    {{ request('payment_status') === 'paid'    ? 'selected' : '' }}>Đã thanh toán</option>
                            </select>
                        </div>

                        <div class="form-group m-b-sm">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fa fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.vehicle_sales.index') }}" class="btn btn-sm btn-default">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH HÓA ĐƠN --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách hóa đơn bán lẻ</h5>
                    <div class="ibox-tools">
                        <span class="label label-info">
                            Tổng: {{ $sales->total() }} hóa đơn
                        </span>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width:90px;">Mã HĐ</th>
                                <th style="width:100px;">Ngày bán</th>
                                <th>Khách hàng</th>
                                <th>Xe / Số khung</th>
                                <th class="text-right">Giá bán</th>
                                {{-- BỎ CỘT ĐÃ THU --}}
                                <th class="text-right">Còn nợ</th>
                                <th>TT thanh toán</th>
                                <th>Hình thức</th>
                                <th > Biển số</th>
                                <th>Thao tác </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    {{-- Mã hóa đơn --}}
                                    <td>
                                        <a href="{{ route('admin.vehicle_sales.show', $sale->id) }}">
                                            <strong>{{ $sale->code }}</strong>
                                        </a>
                                    </td>

                                    {{-- Ngày bán --}}
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>

                                    {{-- Khách hàng --}}
                                    <td>
                                        <strong>{{ optional($sale->customer)->name }}</strong><br>
                                        <small class="text-muted">{{ optional($sale->customer)->phone }}</small>
                                    </td>

                                    {{-- Xe --}}
                                    <td>
                                        {{ optional(optional($sale->vehicle)->model)->name }}
                                        <br>
                                        <small class="text-muted">
                                            {{ optional($sale->vehicle)->frame_no }}
                                        </small>
                                    </td>

                                    {{-- Giá bán --}}
                                    <td class="text-right">
                                        {{ number_format($sale->sale_price, 0, ',', '.') }}
                                    </td>

                                    {{-- Còn nợ --}}
                                    <td class="text-right">
                                        {{ number_format($sale->debt_amount, 0, ',', '.') }}
                                    </td>

                                    {{-- Trạng thái thanh toán --}}
                                    <td>
                                        @if($sale->payment_status === 'paid')
                                            <span class="label label-success">Đã thanh toán</span>
                                        @elseif($sale->payment_status === 'partial')
                                            <span class="label label-warning">Còn nợ</span>
                                        @elseif($sale->payment_status === 'unpaid')
                                            <span class="label label-danger">Chưa thanh toán</span>
                                        @else
                                            <span class="label label-default">{{ $sale->payment_status }}</span>
                                        @endif
                                    </td>

                                    {{-- Hình thức thanh toán --}}
                                    <td>
                                        @php $pm = $sale->payment_method; @endphp
                                        @if($pm === 'cash') Tiền mặt
                                        @elseif($pm === 'bank') Chuyển khoản
                                        @elseif($pm === 'card') Quẹt thẻ
                                        @elseif($pm === 'installment') Trả góp
                                        @elseif($pm === 'other') Khác
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- ======= BIỂN SỐ (CỘT RIÊNG) ======= --}}
                                    <td>
                                        @php
                                            $vehicle = $sale->vehicle;
                                            $plate   = $vehicle ? $vehicle->license_plate : null;
                                        @endphp

                                        @if($plate)
                                            <span class="label label-primary">
                                                {{ $plate }}
                                            </span>
                                        @else
                                            <span class="label label-warning">Chưa có biển</span>

                                            @canModule('vehicles', 'update')
                                            <br>
                                            <button type="button"
                                                    class="btn btn-xs btn-primary m-t-xs btn-input-plate"
                                                    data-sale-id="{{ $sale->id }}"
                                                    data-current-plate=""
                                                    data-vehicle-name="{{ optional(optional($sale->vehicle)->model)->name }}"
                                                    data-frame-no="{{ optional($sale->vehicle)->frame_no }}">
                                                Nhập biển
                                            </button>
                                            @endcanModule
                                        @endif
                                    </td>

                                    {{-- ======= THAO TÁC ======= --}}
                                    <td class="text-center">

                                        {{-- Xem chi tiết --}}
                                        <a href="{{ route('admin.vehicle_sales.payments.create', $sale->id) }}"
                                        class="btn btn-xs btn-info" title="Xem chi tiết">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        {{-- Thu nợ --}}
                                        {{-- @if($sale->debt_amount > 0)
                                            @canModule('vehicle_sales', 'update')
                                                <a href="{{ route('admin.vehicle_sales.payments.create', $sale->id) }}"
                                                class="btn btn-xs btn-warning" title="Thu nợ / trả góp">
                                                    <i class="fa fa-money"></i>
                                                </a>
                                            @endcanModule
                                        @endif --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">
                                        Chưa có hóa đơn bán lẻ nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                        </table>
                    </div>

                    {{-- PHÂN TRANG --}}
                    <div class="text-right">
                        {{ $sales->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL NHẬP BIỂN SỐ --}}
<div class="modal inmodal fade" id="modal-plate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Nhập biển số xe</h4>
                <small class="font-bold" id="plate-modal-subtitle"></small>
            </div>
            <form method="POST" action="{{ route('admin.vehicle_sales.update_plate') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="sale_id" id="plate_sale_id">

                    <div class="form-group">
                        <label>Biển số *</label>
                        <input type="text" name="license_plate" id="license_plate"
                               class="form-control" placeholder="VD: 36B1-123.45" required>
                        <span class="help-block m-b-none">
                            Nhập đúng định dạng để tra cứu sau này dễ hơn.
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu biển số</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Bắt sự kiện click "Nhập biển số"
    $(document).on('click', '.btn-input-plate', function () {
        var saleId   = $(this).data('sale-id');
        var vehicle  = $(this).data('vehicle-name') || '';
        var frameNo  = $(this).data('frame-no') || '';

        $('#plate_sale_id').val(saleId);
        $('#license_plate').val('');
        $('#plate-modal-subtitle').text(vehicle + (frameNo ? ' - Số khung: ' + frameNo : ''));
        $('#modal-plate').modal('show');
    });

});
</script>
@endsection
