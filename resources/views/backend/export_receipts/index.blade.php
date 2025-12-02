@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Danh sách phiếu xuất kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Phiếu xuất kho</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        @canModule('export_receipts', 'create')
            <a href="{{ route('admin.export_receipts.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Tạo phiếu xuất mới
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
                    <form method="GET" action="{{ route('admin.export_receipts.index') }}" class="form-inline">

                        <div class="form-group m-r-sm m-b-sm">
                            <label for="code" class="m-r-xs">Mã phiếu</label>
                            <input type="text" name="code" id="code"
                                   value="{{ request('code') }}"
                                   class="form-control input-sm"
                                   placeholder="VD: PXK_1">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label for="from" class="m-r-xs">Từ ngày</label>
                            <input type="date" name="from" id="from"
                                   value="{{ request('from') }}"
                                   class="form-control input-sm">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label for="to" class="m-r-xs">Đến ngày</label>
                            <input type="date" name="to" id="to"
                                   value="{{ request('to') }}"
                                   class="form-control input-sm">
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label for="warehouse_id" class="m-r-xs">Kho</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control input-sm">
                                <option value="">-- Tất cả kho --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ (string)request('warehouse_id') === (string)$warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label for="export_type" class="m-r-xs">Loại xuất</label>
                            <select name="export_type" id="export_type" class="form-control input-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="sell" {{ request('export_type') === 'sell' ? 'selected' : '' }}>Bán buôn</option>
                                <option value="transfer" {{ request('export_type') === 'transfer' ? 'selected' : '' }}>Chuyển kho</option>
                                <option value="demo" {{ request('export_type') === 'demo' ? 'selected' : '' }}>Demo / sự kiện</option>
                            </select>
                        </div>

                        <div class="form-group m-r-sm m-b-sm">
                            <label for="supplier_id" class="m-r-xs">Đối tác (Nhà cung cấp)</label>
                            <select name="supplier_id" id="supplier_id" class="form-control input-sm">
                                <option value="">-- Tất cả --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ (string)request('supplier_id') === (string)$supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group m-b-sm">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fa fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.export_receipts.index') }}" class="btn btn-sm btn-default">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH PHIẾU XUẤT --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách phiếu xuất kho</h5>
                    <div class="ibox-tools">
                        <span class="label label-info">
                            Tổng: {{ $receipts->total() }} phiếu
                        </span>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 80px;">Mã phiếu</th>
                                <th style="width: 100px;">Ngày xuất</th>
                                <th>Kho xuất</th>
                                <th>Loại xuất</th>
                                <th>Đối tác nhận xe</th>
                                <th class="text-right">Tổng tiền</th>
                                <th class="text-right">Đã thu</th>
                                <th class="text-right">Còn nợ</th>
                                <th>TT thanh toán</th>
                                <th style="width: 90px;">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($receipts as $receipt)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.export_receipts.show', $receipt->id) }}">
                                            <strong>{{ $receipt->code }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ optional($receipt->export_date ?? $receipt->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ optional($receipt->warehouse)->name }}</td>
                                    <td>
                                        @if($receipt->export_type === 'sell')
                                            <span class="label label-primary">Bán buôn</span>
                                        @elseif($receipt->export_type === 'transfer')
                                            <span class="label label-info">Chuyển kho</span>
                                        @elseif($receipt->export_type === 'demo')
                                            <span class="label label-warning">Demo / sự kiện</span>
                                        @else
                                            <span class="label label-default">{{ $receipt->export_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($receipt->export_type === 'sell')
                                            {{ optional($receipt->supplier)->name ?? 'Không rõ' }}
                                        @else
                                            <span class="text-muted">Không áp dụng</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($receipt->total_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($receipt->paid_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($receipt->debt_amount ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php $status = $receipt->payment_status; @endphp
                                        @if($status === 'paid_docs')
                                            {{-- ĐÃ THANH TOÁN & GIAO GIẤY TỜ --}}
                                            <span class="label label-success">
                                                Đã thanh toán & giao giấy tờ
                                            </span>
                                        @elseif($status === 'paid')
                                            {{-- ĐÃ NHẬN ĐỦ TIỀN, CHƯA GIAO GIẤY --}}
                                            <span class="label label-primary">
                                                Đã thanh toán (chưa giao giấy)
                                            </span>
                                        @elseif($status === 'partial')
                                            <span class="label label-warning">Thanh toán một phần</span>
                                        @elseif($status === 'unpaid')
                                            <span class="label label-danger">Chưa thanh toán</span>
                                        @else
                                            <span class="label label-default">{{ $status ?? 'Không rõ' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.export_receipts.show', $receipt->id) }}"
                                           class="btn btn-xs btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Nếu sau này anh muốn xoá / sửa thì thêm ở đây --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        Chưa có phiếu xuất kho nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PHÂN TRANG --}}
                    <div class="text-right">
                        {{ $receipts->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
