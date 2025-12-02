@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Phiếu điều chỉnh tồn kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Điều chỉnh tồn kho</strong></li>
        </ol>
    </div>
    <div class="col-lg-3 text-right m-t-lg"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox">
        <div class="ibox-title">
            <h5>Bộ lọc</h5>
            <div class="ibox-tools"><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div>
        </div>
        <div class="ibox-content" style="display:none;">
            <form method="GET" class="form-inline">

                <input type="text" name="code" class="form-control m-r-sm"
                       placeholder="Mã phiếu DC_x"
                       value="{{ request('code') }}">

                <select name="warehouse_id" class="form-control m-r-sm">
                    <option value="">-- Chọn kho --</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}"
                            {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>

                <input type="date" name="from" class="form-control m-r-sm"
                       value="{{ request('from') }}">

                <input type="date" name="to" class="form-control m-r-sm"
                       value="{{ request('to') }}">

                <button class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Lọc</button>
                <a href="{{ route('admin.inventory_adjustments.index') }}"
                   class="btn btn-default btn-sm">Reset</a>
            </form>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-title">
            <h5>Danh sách phiếu điều chỉnh</h5>
            <div class="ibox-tools">
                <span class="label label-info">
                    Tổng: {{ $adjustments->total() }} phiếu
                </span>
            </div>
        </div>
        <div class="ibox-content">

            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Mã</th>
                    <th>Ngày</th>
                    <th>Kho</th>
                    <th>Phiếu kiểm kê</th>
                    <th>Người tạo</th>
                    <th class="text-center" style="width:100px;">Xem</th>
                </tr>
                </thead>
                <tbody>
                @foreach($adjustments as $adj)
                    <tr>
                        <td><strong>{{ $adj->code }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($adj->adjustment_date)->format('d/m/Y') }}</td>
                        <td>{{ optional($adj->warehouse)->name }}</td>
                        <td>
                            @if($adj->stockTake)
                                <a href="{{ route('admin.stock_takes.show', $adj->stockTake->id) }}">
                                    {{ $adj->stockTake->code }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ optional($adj->createdBy)->name }}</td>

                        <td class="text-center">
                            <a href="{{ route('admin.inventory_adjustments.show', $adj->id) }}"
                               class="btn btn-xs btn-info">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="text-right">
                {{ $adjustments->appends(request()->query())->links() }}
            </div>

        </div>
    </div>

</div>

@endsection
