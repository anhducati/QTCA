@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Thu nợ / trả góp</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.vehicle_sales.index') }}">Bán lẻ xe</a></li>
            <li><a href="{{ route('admin.vehicle_sales.show', $sale->id) }}">{{ $sale->code }}</a></li>
            <li class="active"><strong>Thu nợ</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.vehicle_sales.show', $sale->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại HĐ {{ $sale->code }}
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-8">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thu nợ / trả góp cho hóa đơn {{ $sale->code }}</h5>
                </div>
                <div class="ibox-content">

                    @include('layouts.message')

                    {{-- TÓM TẮT CÔNG NỢ --}}
                    <div class="alert alert-info">
                        <p>
                            <strong>Giá bán:</strong>
                            {{ number_format($sale->sale_price, 0, ',', '.') }} VNĐ<br>
                            <strong>Đã thu:</strong>
                            {{ number_format($sale->paid_amount, 0, ',', '.') }} VNĐ<br>
                            <strong>Còn nợ:</strong>
                            <span class="text-danger">
                                {{ number_format($sale->debt_amount, 0, ',', '.') }} VNĐ
                            </span>
                        </p>
                    </div>

                    {{-- FORM THU NỢ --}}
                    <form method="POST" action="{{ route('admin.vehicle_sales.payments.store', $sale->id) }}">
                        @csrf

                        <div class="row m-b-sm">
                            <div class="col-md-4">
                                <label>Ngày thu *</label>
                                <input type="date" name="payment_date" class="form-control"
                                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Số tiền thu (VNĐ) *</label>
                                <input type="text" name="amount" id="amount"
                                       class="form-control"
                                       placeholder="VD: 5.000.000"
                                       value="{{ old('amount') }}" required>
                                @error('amount')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Hình thức thanh toán *</label>
                                <select name="method" class="form-control" required>
                                    <option value="">-- Chọn --</option>
                                    <option value="cash"        {{ old('method')=='cash'?'selected':'' }}>Tiền mặt</option>
                                    <option value="bank"        {{ old('method')=='bank'?'selected':'' }}>Chuyển khoản</option>
                                    <option value="card"        {{ old('method')=='card'?'selected':'' }}>Quẹt thẻ</option>
                                    <option value="installment" {{ old('method')=='installment'?'selected':'' }}>Trả góp</option>
                                    <option value="other"       {{ old('method')=='other'?'selected':'' }}>Khác</option>
                                </select>
                                @error('method')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row m-b-sm">
                            <div class="col-md-12">
                                <label>Ghi chú thanh toán</label>
                                <input type="text" name="note" class="form-control"
                                       placeholder="VD: Khách trả góp kỳ 2, CK Vietcombank..."
                                       value="{{ old('note') }}">
                                <small class="text-muted">
                                    Ghi chú này sẽ được ghi vào lịch sử thanh toán,  
                                    và (trong controller) em sẽ append thêm vào <strong>note</strong> của HĐ dạng
                                    <code>noidung_noidung</code> như anh yêu cầu.
                                </small>
                            </div>
                        </div>

                        <div class="text-right m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Lưu phiếu thu nợ
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>

        {{-- LỊCH SỬ CÁC LẦN THU --}}
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Lịch sử thu nợ</h5>
                </div>
                <div class="ibox-content">
                    @if($sale->payments->count() == 0)
                        <p class="text-muted">
                            Chưa có lần thu nợ / trả góp nào.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th class="text-right">Tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sale->payments as $p)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
                                        <td class="text-right">
                                            {{ number_format($p->amount, 0, ',', '.') }}
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

@section('page-scripts')
<script>
    // Format tiền nhẹ nhàng phía client (không bắt buộc)
    function parseMoney(str) {
        if (!str) return 0;
        str = String(str).replace(/\D/g, '');
        return str ? parseInt(str, 10) : 0;
    }
    function formatMoney(num) {
        if (!num || isNaN(num)) return '';
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var amountInput = document.getElementById('amount');
        if (!amountInput) return;

        amountInput.addEventListener('blur', function () {
            var v = parseMoney(this.value);
            this.value = v ? formatMoney(v) : '';
        });
    });
</script>
@endsection
