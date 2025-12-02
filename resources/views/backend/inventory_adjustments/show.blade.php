{{-- resources/views/backend/inventory_adjustments/show.blade.php --}}
@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Phiếu điều chỉnh tồn kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.inventory_adjustments.index') }}">Điều chỉnh tồn kho</a></li>
            <li class="active">
                <strong>{{ $adjustment->code }}</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.inventory_adjustments.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>

        @if($adjustment->stockTake)
            <a href="{{ route('admin.stock_takes.show', $adjustment->stockTake->id) }}"
               class="btn btn-info">
                <i class="fa fa-list"></i> Xem phiếu kiểm kê {{ $adjustment->stockTake->code }}
            </a>
        @endif
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    {{-- THÔNG TIN CHUNG --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin phiếu điều chỉnh</h5>
                </div>
                <div class="ibox-content">

                    <table class="table table-borderless m-b-none">
                        <tr>
                            <th style="width:150px;">Mã phiếu</th>
                            <td><strong>{{ $adjustment->code }}</strong></td>
                        </tr>
                        <tr>
                            <th>Ngày điều chỉnh</th>
                            <td>
                                {{ $adjustment->adjustment_date
                                    ? \Carbon\Carbon::parse($adjustment->adjustment_date)->format('d/m/Y')
                                    : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Kho</th>
                            <td>{{ optional($adjustment->warehouse)->name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lý do</th>
                            <td>{{ $adjustment->reason ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Phiếu kiểm kê</th>
                            <td>
                                @if($adjustment->stockTake)
                                    <a href="{{ route('admin.stock_takes.show', $adjustment->stockTake->id) }}">
                                        {{ $adjustment->stockTake->code }}
                                    </a>
                                @else
                                    <span class="text-muted">Không gắn với phiếu kiểm kê</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ optional($adjustment->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Ghi chú</th>
                            <td>{{ $adjustment->note ?: '-' }}</td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

        {{-- DANH SÁCH DÒNG ĐIỀU CHỈNH --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách xe điều chỉnh</h5>
                </div>
                <div class="ibox-content">

                    @php
                        $items = $adjustment->items ?? collect();
                    @endphp

                    @if($items->isEmpty())
                        <p class="text-muted m-b-none">
                            Phiếu điều chỉnh này chưa có dòng nào.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered m-b-none">
                                <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Số khung</th>
                                    <th>Số máy</th>
                                    <th style="width:120px;">Hành động</th>
                                    <th>Ghi chú</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            @if($item->vehicle)
                                                <a href="{{ route('admin.vehicles.show', $item->vehicle->id) }}">
                                                    {{ $item->vehicle->frame_no }}
                                                </a>
                                            @else
                                                {{ $item->frame_no ?: '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->vehicle)
                                                {{ $item->vehicle->engine_no ?: '-' }}
                                            @else
                                                {{ $item->engine_no ?: '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->action === 'add')
                                                <span class="label label-success">Nhập thêm</span>
                                            @elseif($item->action === 'remove')
                                                <span class="label label-danger">Xuất bớt</span>
                                            @else
                                                <span class="label label-default">{{ $item->action }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->note ?: '-' }}</td>
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
