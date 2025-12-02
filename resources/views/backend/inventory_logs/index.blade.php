{{-- resources/views/backend/inventory_logs/index.blade.php --}}
@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Nhật ký tồn kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Nhật ký tồn kho</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        {{-- Không cho tạo/sửa/xóa trực tiếp nhật ký, chỉ xem --}}
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
    <div class="row m-b-sm">
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
                    <form method="GET" action="{{ route('admin.inventory_logs.index') }}" class="form-inline">

                        {{-- Kho --}}
                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Kho</label>
                            <select name="warehouse_id" class="form-control input-sm">
                                <option value="">-- Tất cả kho --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loại log --}}
                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Loại</label>
                            <select name="log_type" class="form-control input-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="import"     {{ request('log_type')=='import' ? 'selected' : '' }}>Nhập kho</option>
                                <option value="export"     {{ request('log_type')=='export' ? 'selected' : '' }}>Xuất kho</option>
                                <option value="transfer"   {{ request('log_type')=='transfer' ? 'selected' : '' }}>Chuyển kho</option>
                                <option value="sale"       {{ request('log_type')=='sale' ? 'selected' : '' }}>Bán lẻ</option>
                                <option value="demo"       {{ request('log_type')=='demo' ? 'selected' : '' }}>Demo / sự kiện</option>
                                <option value="adjustment" {{ request('log_type')=='adjustment' ? 'selected' : '' }}>Điều chỉnh</option>
                            </select>
                        </div>

                        {{-- Số khung --}}
                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Số khung</label>
                            <input type="text" name="frame_no"
                                   value="{{ request('frame_no') }}"
                                   class="form-control input-sm"
                                   placeholder="Nhập 1 phần số khung">
                        </div>

                        {{-- Biển số --}}
                        <div class="form-group m-r-sm m-b-sm">
                            <label class="m-r-xs">Biển số</label>
                            <input type="text" name="license_plate"
                                   value="{{ request('license_plate') }}"
                                   class="form-control input-sm"
                                   placeholder="VD: 36B1-">
                        </div>

                        {{-- Ngày từ / đến --}}
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

                        <div class="form-group m-b-sm">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fa fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.inventory_logs.index') }}" class="btn btn-sm btn-default">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH NHẬT KÝ --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách nhật ký tồn kho</h5>
                    <div class="ibox-tools">
                        <span class="label label-info">
                            Tổng: {{ $logs->total() }} dòng
                        </span>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width:140px;">Thời gian</th>
                                <th>Xe / Số khung</th>
                                <th>Biển số</th>
                                <th>Loại</th>
                                <th>Từ kho</th>
                                <th>Đến kho</th>
                                <th>Tham chiếu</th>
                                <th>Người thực hiện</th>
                                <th>Ghi chú</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $log)
                                @php
                                    $v  = $log->vehicle;
                                    $fw = $log->fromWarehouse;
                                    $tw = $log->toWarehouse;
                                @endphp
                                <tr>
                                    {{-- Thời gian --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($log->log_date)->format('d/m/Y H:i') }}
                                    </td>

                                    {{-- Xe --}}
                                    <td>
                                        @if($v)
                                            <strong>{{ optional($v->model)->name }}</strong><br>
                                            <small class="text-muted">
                                                Số khung: {{ $v->frame_no }}<br>
                                                Số máy: {{ $v->engine_no ?? '-' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Không có thông tin xe</span>
                                        @endif
                                    </td>

                                    {{-- Biển số --}}
                                    <td>
                                        @if($v && $v->license_plate)
                                            {{ $v->license_plate }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Loại log --}}
                                    <td>
                                        @php $t = $log->log_type; @endphp
                                        @if($t === 'import')
                                            <span class="label label-primary">Nhập kho</span>
                                        @elseif($t === 'export')
                                            <span class="label label-danger">Xuất kho</span>
                                        @elseif($t === 'transfer')
                                            <span class="label label-warning">Chuyển kho</span>
                                        @elseif($t === 'sale')
                                            <span class="label label-success">Bán lẻ</span>
                                        @elseif($t === 'demo')
                                            <span class="label label-info">Demo / sự kiện</span>
                                        @elseif($t === 'adjustment')
                                            <span class="label label-default">Điều chỉnh</span>
                                        @else
                                            <span class="label label-default">{{ $t }}</span>
                                        @endif
                                    </td>

                                    {{-- Từ kho --}}
                                    <td>
                                        {{ $fw ? $fw->name : '-' }}
                                    </td>

                                    {{-- Đến kho --}}
                                    <td>
                                        {{ $tw ? $tw->name : '-' }}
                                    </td>

                                    {{-- Tham chiếu (phiếu nhập / xuất / kiểm kê / điều chỉnh / bán lẻ) --}}
                                    <td>
                                        @php
                                            $refTable = $log->ref_table;
                                            $refId    = $log->ref_id;
                                        @endphp

                                        @if($refTable && $refId)
                                            @if($refTable === 'import_receipts')
                                                <a href="{{ route('admin.import_receipts.show', $refId) }}">
                                                    Phiếu nhập #{{ $refId }}
                                                </a>
                                            @elseif($refTable === 'export_receipts')
                                                <a href="{{ route('admin.export_receipts.show', $refId) }}">
                                                    Phiếu xuất #{{ $refId }}
                                                </a>
                                            @elseif($refTable === 'stock_takes')
                                                <a href="{{ route('admin.stock_takes.show', $refId) }}">
                                                    Kiểm kê #{{ $refId }}
                                                </a>
                                            @elseif($refTable === 'inventory_adjustments')
                                                <a href="{{ route('admin.inventory_adjustments.show', $refId) }}">
                                                    Điều chỉnh #{{ $refId }}
                                                </a>
                                            @elseif($refTable === 'vehicle_sales')
                                                <a href="{{ route('admin.vehicle_sales.show', $refId) }}">
                                                    HĐ bán lẻ #{{ $refId }}
                                                </a>
                                            @else
                                                {{ $refTable }} #{{ $refId }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Người thực hiện --}}
                                    <td>
                                        {{ optional($log->createdBy)->name ?? '-' }}
                                    </td>

                                    {{-- Ghi chú --}}
                                    <td>
                                        {{ $log->note ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Chưa có nhật ký tồn kho nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PHÂN TRANG --}}
                    <div class="text-right">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
