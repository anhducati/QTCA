{{-- resources/views/backend/vehicle_sales/payment_form.blade.php --}}
@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Chi tiết hóa đơn</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.vehicle_sales.index') }}">Bán lẻ xe</a></li>
            <li class="active"><strong>Chi tiết hóa đơn – {{ $sale->code }}</strong></li>
        </ol>
    </div>

    {{-- Nút XEM HÓA ĐƠN BÁN LẺ – ADDED --}}
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.vehicle_sales.show', $sale->id) }}"
           class="btn btn-info">
            <i class="fa fa-file-text-o"></i> Xem hóa đơn bán lẻ
        </a>

        <a href="{{ route('admin.vehicle_sales.index') }}"
           class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Danh sách
        </a>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">

    {{-- THÔNG BÁO --}}
    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-b-none">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>


    <div class="row">

        {{-- THÔNG TIN HÓA ĐƠN --}}
        <div class="col-lg-7">

            {{-- INFO --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin hóa đơn bán lẻ</h5>
                </div>
                <div class="ibox-content">

                    @php
                        $price = number_format($sale->sale_price, 0, ',', '.');
                        $paid  = number_format($sale->paid_amount, 0, ',', '.');
                        $debt  = number_format($sale->debt_amount, 0, ',', '.');
                        $plate = optional($sale->vehicle)->license_plate;
                    @endphp

                    <table class="table table-borderless m-b-none">
                        <tr>
                            <th style="width:150px;">Mã HĐ</th>
                            <td><strong>{{ $sale->code }}</strong></td>
                        </tr>

                        <tr>
                            <th>Ngày bán</th>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                        </tr>

                        <tr>
                            <th>Khách hàng</th>
                            <td>
                                <strong>{{ $sale->customer->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $sale->customer->phone }}
                                    @if($sale->customer->address)
                                        · {{ $sale->customer->address }}
                                    @endif
                                </small>
                            </td>
                        </tr>

                        <tr>
                            <th>Xe</th>
                            <td>
                                {{ optional(optional($sale->vehicle)->model)->name }}
                                ({{ optional($sale->vehicle->color)->name }})
                                <br>
                                <small class="text-muted">
                                    Số khung: {{ $sale->vehicle->frame_no }}
                                    · Số máy: {{ $sale->vehicle->engine_no }}
                                </small>
                            </td>
                        </tr>

                        {{-- BIỂN SỐ – ADDED --}}
                        <tr>
                            <th>Biển số</th>
                            <td>
                                @if($plate)
                                    <span class="label label-primary">{{ $plate }}</span>
                                @else
                                    <span class="label label-warning">Chưa có biển</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Giá bán</th>
                            <td><strong>{{ $price }}</strong> VNĐ</td>
                        </tr>

                        <tr>
                            <th>Đã thu</th>
                            <td class="text-navy"><strong>{{ $paid }}</strong> VNĐ</td>
                        </tr>

                        <tr>
                            <th>Còn nợ</th>
                            <td class="text-danger">
                                <strong>{{ $debt }}</strong> VNĐ
                            </td>
                        </tr>

                        <tr>
                            <th>Hình thức</th>
                            <td>
                                @if($sale->payment_method === 'cash') Tiền mặt
                                @elseif($sale->payment_method === 'bank') Chuyển khoản
                                @elseif($sale->payment_method === 'card') Quẹt thẻ
                                @elseif($sale->payment_method === 'installment') Trả góp
                                @else Khác
                                @endif
                            </td>
                        </tr>

                        @if($sale->note)
                            <tr>
                                <th>Ghi chú</th>
                                <td>{{ $sale->note }}</td>
                            </tr>
                        @endif

                    </table>

                </div>
            </div>


            {{-- LỊCH SỬ THANH TOÁN --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Lịch sử Chi tiết hóa đơn</h5>
                </div>
                <div class="ibox-content">

                    @php $payments = $sale->payments ?? collect(); @endphp

                    @if($payments->isEmpty())
                        <p class="text-muted m-b-none">Chưa có lần thu nợ nào.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width:110px;">Ngày thu</th>
                                <th class="text-right">Số tiền</th>
                                <th>Hình thức</th>
                                <th>Ghi chú</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $p)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') }}</td>
                                        <td class="text-right">
                                            {{ number_format($p->amount, 0, ',', '.') }}
                                        </td>
                                        <td>{{ ucfirst($p->method) }}</td>
                                        <td>{{ $p->note }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>

        </div> {{-- END LEFT COLUMN --}}



        {{-- FORM THU NỢ --}}
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Ghi nhận Chi tiết hóa đơn</h5>
                </div>
                <div class="ibox-content">

                    @if($sale->debt_amount <= 0)
                        <div class="alert alert-success">
                            Hóa đơn này đã thanh toán đủ.
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('admin.vehicle_sales.payments.store', $sale->id) }}">
                        @csrf

                        <div class="form-group">
                            <label>Ngày thu *</label>
                            <input type="date" name="payment_date" class="form-control"
                                   value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Số tiền thu *</label>
                            <input type="text" id="amount" name="amount"
                                   class="form-control money-input"
                                   value="{{ old('amount') }}"
                                   placeholder="Nhập số tiền VNĐ" required>
                        </div>

                        <div class="form-group">
                            <label>Hình thức thanh toán *</label>
                            <select name="method" id="method" class="form-control" required>
                                <option value="cash">Tiền mặt</option>
                                <option value="bank">Chuyển khoản</option>
                                <option value="card">Quẹt thẻ</option>
                                <option value="installment">Trả góp</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>

                        {{-- Ô nợ trả góp --}}
                        <div class="form-group" id="installment-debt-wrapper" style="display:none;">
                            <label>Số tiền nợ trả góp *</label>
                            <input type="text" name="installment_debt" id="installment_debt"
                                   class="form-control money-input"
                                   placeholder="VD: 10.000.000">
                        </div>

                        {{-- Note --}}
                        <div class="form-group">
                            <label>Ghi chú <span id="note-required" class="text-danger" style="display:none;">*</span></label>
                            <textarea name="note" id="note" rows="3" class="form-control"
                                      placeholder="VD: Trả góp qua HD Saison..."></textarea>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary"
                                    {{ $sale->debt_amount <= 0 ? 'disabled' : '' }}>
                                <i class="fa fa-check"></i> Ghi nhận thu nợ
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>

</div>



@endsection


@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function parseMoney(str) {
        if (!str) return 0;
        return parseInt(String(str).replace(/\D/g, '')) || 0;
    }

    function formatMoney(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    document.querySelectorAll('.money-input').forEach(function (input) {
        input.addEventListener('input', function () {
            let v = parseMoney(this.value);
            this.value = v ? formatMoney(v) : '';
        });
    });

    const methodSelect     = document.getElementById('method');
    const installmentWrap  = document.getElementById('installment-debt-wrapper');
    const installmentInput = document.getElementById('installment_debt');
    const noteInput        = document.getElementById('note');
    const noteRequiredSpan = document.getElementById('note-required');

    function updateInstallmentUI() {
        if (methodSelect.value === 'installment') {
            installmentWrap.style.display = 'block';
            installmentInput.required = true;
            noteInput.required        = true;
            noteRequiredSpan.style.display = 'inline';
        } else {
            installmentWrap.style.display = 'none';
            installmentInput.required = false;
            noteInput.required        = false;
            noteRequiredSpan.style.display = 'none';
        }
    }

    methodSelect.addEventListener('change', updateInstallmentUI);
    updateInstallmentUI();
});
</script>
@endsection
