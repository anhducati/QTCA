@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Tạo phiếu kiểm kê</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.stock_takes.index') }}">Kiểm kê kho</a></li>
            <li class="active"><strong>Tạo mới</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.stock_takes.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin phiếu kiểm kê</h5>
                </div>
                <div class="ibox-content">
                    @include('layouts.message')

                    <form method="POST" action="{{ route('admin.stock_takes.store') }}">
                        @csrf

                        <div class="form-group">
                            <label>Kho cần kiểm kê *</label>
                            <select name="warehouse_id" class="form-control" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}"
                                        {{ old('warehouse_id')==$wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ngày kiểm kê *</label>
                            <input type="date" name="stock_take_date" class="form-control"
                                   value="{{ old('stock_take_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" rows="3" class="form-control"
                                      placeholder="VD: Kiểm kê định kỳ cuối tháng...">{{ old('note') }}</textarea>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Tạo phiếu & load danh sách xe
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
