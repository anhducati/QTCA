@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Bán lẻ xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Bán lẻ xe</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Hóa đơn bán lẻ</h5>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.vehicle_sales.store') }}" method="POST" id="sale-form">
                        @csrf

                        {{-- ========== 1. THÔNG TIN XE ========== --}}
                        <h4>1. Thông tin xe</h4>
                        <div class="row m-b-sm">
                            <div class="col-md-4">
                                <label>Số khung (VIN) *</label>
                                @php
                                    $oldVehicleId = old('vehicle_id');
                                @endphp
                                <select id="vehicle_select" class="form-control slim-select" required>
                                    <option value="">-- Gõ số khung / model để tìm --</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}"
                                                data-frame-no="{{ $v->frame_no }}"
                                                data-model-name="{{ optional($v->model)->name }}"
                                                data-brand-name="{{ optional(optional($v->model)->brand)->name }}"
                                                data-color-name="{{ optional($v->color)->name }}"
                                                data-warehouse="{{ optional($v->warehouse)->name }}"
                                                {{ $oldVehicleId == $v->id ? 'selected' : '' }}
                                        >
                                            {{ $v->frame_no }}
                                            - {{ optional($v->model)->name }}
                                            @if(optional($v->color)->name)
                                                ({{ optional($v->color)->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden" name="vehicle_id" id="vehicle_id"
                                       value="{{ $oldVehicleId }}">

                                <small class="text-muted">
                                    Xe phải đang trong kho (chưa bán) mới bán lẻ được.
                                </small>
                                @error('vehicle_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Model / Dòng xe</label>
                                <p class="form-control-static" id="vehicle_model_text">
                                    <span class="text-muted">Chưa chọn xe</span>
                                </p>
                            </div>

                            <div class="col-md-4">
                                <label>Màu / Kho</label>
                                <p class="form-control-static" id="vehicle_color_text">
                                    <span class="text-muted">Chưa chọn xe</span>
                                </p>
                            </div>
                        </div>

                        {{-- Ghi chú xe --}}
                        <div class="row m-b-sm">
                            <div class="col-md-12">
                                <label>Ghi chú xe</label>
                                <input type="text" name="vehicle_note" class="form-control"
                                       placeholder="VD: xe tặng mũ, áo mưa, KM đặc biệt..."
                                       value="{{ old('vehicle_note') }}">
                            </div>
                        </div>

                        {{-- ĐÃ BỎ: Giá nhập tham khảo / Giá bán tip / Biển số (nếu đã có) --}}

                        <hr>

                        {{-- ========== 2. THÔNG TIN KHÁCH HÀNG ========== --}}
                        <h4>2. Khách hàng</h4>
                        <div class="row m-b-sm">

                            <div class="col-md-4">
                                <label>Số điện thoại *</label>
                                <div class="input-group">
                                    <input type="text" id="customer_phone" name="customer_phone"
                                           class="form-control"
                                           value="{{ old('customer_phone') }}"
                                           placeholder="VD: 09xx..."
                                           autocomplete="off" required>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" type="button" id="btn-find-customer">
                                            <i class="fa fa-search"></i> Kiểm tra
                                        </button>
                                    </span>
                                </div>
                                @error('customer_phone')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                                <small id="customer_status" class="text-muted"></small>

                                <input type="hidden" id="customer_id" name="customer_id">
                            </div>

                            <div class="col-md-4">
                                <label>Tên khách hàng *</label>
                                <input type="text" id="customer_name" name="customer_name"
                                       class="form-control"
                                       value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Địa chỉ</label>
                                <input type="text" id="customer_address" name="customer_address"
                                       class="form-control"
                                       value="{{ old('customer_address') }}">
                            </div>
                        </div>

                        <div class="row m-b-sm">
                            <div class="col-md-8">
                                <label>Ghi chú khách hàng</label>
                                <input type="text" id="customer_note" name="customer_note"
                                       class="form-control"
                                       value="{{ old('customer_note') }}">
                            </div>

                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-default btn-block" id="btn-toggle-edit-customer">
                                    <i class="fa fa-pencil"></i> Cho phép sửa thông tin KH
                                </button>
                            </div>
                        </div>

                        <hr>

                    {{-- ========== 3. THANH TOÁN ========== --}}
                    <h4>3. Thanh toán</h4>
                    <div class="row m-b-sm">

                        <div class="col-md-3">
                            <label>Ngày bán *</label>
                            <input type="date" name="sale_date" class="form-control"
                                value="{{ old('sale_date', date('Y-m-d')) }}" required>
                            @error('sale_date')
                                <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Giá bán (VNĐ) *</label>
                            <input type="text" name="sale_price" id="sale_price"
                                class="form-control money-input"
                                placeholder="VD: 30.000.000"
                                value="{{ old('sale_price') }}" required>
                            @error('sale_price')
                                <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label>Khách trả (VNĐ) *</label>
                            <input type="text" name="paid_amount" id="paid_amount"
                                class="form-control money-input"
                                placeholder="VD: 20.000.000"
                                value="{{ old('paid_amount') }}" required>
                        </div>

                        <div class="col-md-3">
                            <label>Còn lại (nợ) (VNĐ) *</label>
                            <input type="text" name="debt_amount" id="debt_amount"
                                class="form-control money-input"
                                placeholder="Tự tính"
                                value="{{ old('debt_amount') }}" required readonly>
                        </div>
                    </div>

                    <div class="row m-b-sm">
                        <div class="col-md-4">
                            <label>Hình thức thanh toán *</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="">-- Chọn --</option>
                                <option value="cash"        {{ old('payment_method')=='cash'?'selected':'' }}>Tiền mặt</option>
                                <option value="bank"        {{ old('payment_method')=='bank'?'selected':'' }}>Chuyển khoản</option>
                                <option value="card"        {{ old('payment_method')=='card'?'selected':'' }}>Quẹt thẻ</option>
                                <option value="installment" {{ old('payment_method')=='installment'?'selected':'' }}>Trả góp</option>
                                <option value="other"       {{ old('payment_method')=='other'?'selected':'' }}>Khác</option>
                            </select>
                        </div>

                        {{-- Ô nhập số nợ trả góp (chỉ hiện khi chọn Trả góp) --}}
                        <div class="col-md-4" id="installment-debt-wrapper" style="display:none;">
                            <label>Số nợ trả góp *</label>
                            <input type="text" id="installment_debt" class="form-control money-input"
                                placeholder="VD: 25.000.000">
                        </div>

                        <div class="col-md-4">
                            <label>Ghi chú hóa đơn <span id="sale-note-required" class="text-danger" style="display:none;">*</span></label>
                            <input type="text" name="sale_note" id="sale_note" class="form-control"
                                value="{{ old('sale_note') }}">
                        </div>
                    </div>


                        <div class="text-right m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Lưu hóa đơn bán lẻ
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

{{-- SlimSelect --}}
<link href="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== SLIMSELECT CHO SỐ KHUNG ==========
    let vehicleSS = new SlimSelect({
        select: '#vehicle_select',
        settings: {
            searchPlaceholder: 'Gõ số khung / model...'
        }
    });

    function updateVehicleInfoFromSelect() {
        let select = document.getElementById('vehicle_select');
        let option = select.options[select.selectedIndex];

        if (!option || !option.value) {
            document.getElementById('vehicle_id').value = '';
            document.getElementById('vehicle_model_text').innerHTML =
                '<span class="text-muted">Chưa chọn xe</span>';
            document.getElementById('vehicle_color_text').innerHTML =
                '<span class="text-muted">Chưa chọn xe</span>';
            return;
        }

        let vehicleId = option.value;
        let model     = option.getAttribute('data-model-name')   || '';
        let brand     = option.getAttribute('data-brand-name')   || '';
        let color     = option.getAttribute('data-color-name')   || '';
        let wh        = option.getAttribute('data-warehouse')    || '';

        document.getElementById('vehicle_id').value = vehicleId;

        document.getElementById('vehicle_model_text').innerHTML =
            '<strong>' + (model || '') + '</strong>' +
            (brand ? ' - <span class="text-muted">' + brand + '</span>' : '');

        document.getElementById('vehicle_color_text').innerHTML =
            (color || '-') + ' / ' + (wh || '-');
    }

    // khi đổi lựa chọn xe
    $('#vehicle_select').on('change', updateVehicleInfoFromSelect);
    // init theo old()
    updateVehicleInfoFromSelect();


    // ========== LOCK/UNLOCK customer fields ==========
    let customerLocked = true;
    const lockFields = () => {
        ['customer_name','customer_address','customer_note'].forEach(id => {
            document.getElementById(id).setAttribute('readonly','readonly');
        });
    };
    const unlockFields = () => {
        ['customer_name','customer_address','customer_note'].forEach(id => {
            document.getElementById(id).removeAttribute('readonly');
        });
    };

    lockFields();

    document.getElementById('btn-toggle-edit-customer').addEventListener('click', function () {
        customerLocked = !customerLocked;
        if (customerLocked) {
            lockFields();
            this.innerHTML = '<i class="fa fa-pencil"></i> Cho phép sửa thông tin KH';
        } else {
            unlockFields();
            this.innerHTML = '<i class="fa fa-lock"></i> Khóa thông tin KH';
        }
    });

    // ========== FIND CUSTOMER BY PHONE ==========
    $('#btn-find-customer').on('click', function () {
        let phone = $('#customer_phone').val().trim();
        if (!phone) {
            alert('Vui lòng nhập số điện thoại.');
            return;
        }

        $('#customer_status').text('Đang kiểm tra...');

        $.get('{{ route('admin.vehicle_sales.find_customer') }}', { phone: phone })
            .done(function (res) {
                if (!res.success) {
                    $('#customer_status').text('Khách mới, vui lòng nhập thông tin.');
                    $('#customer_id').val('');
                    unlockFields();
                    customerLocked = false;
                    $('#btn-toggle-edit-customer').html('<i class="fa fa-lock"></i> Khóa thông tin KH');
                    return;
                }

                let c = res.data;
                $('#customer_status').text('Đã tìm thấy khách cũ.');
                $('#customer_id').val(c.id);
                $('#customer_name').val(c.name || '');
                $('#customer_address').val(c.address || '');
                $('#customer_note').val(c.note || '');

                lockFields();
                customerLocked = true;
                $('#btn-toggle-edit-customer').html('<i class="fa fa-pencil"></i> Cho phép sửa thông tin KH');
            })
            .fail(function (xhr) {
                $('#customer_status').text('');
                if (xhr.status === 404) {
                    $('#customer_status').text('Khách mới, vui lòng nhập thông tin.');
                    $('#customer_id').val('');
                    unlockFields();
                    customerLocked = false;
                    $('#btn-toggle-edit-customer').html('<i class="fa fa-lock"></i> Khóa thông tin KH');
                } else {
                    alert('Lỗi kiểm tra khách hàng.');
                }
            });
    });


    // ========== LOGIC TRẢ GÓP & GHI CHÚ ==========
    const paymentSelect          = document.getElementById('payment_method');
    const installmentWrapper     = document.getElementById('installment-debt-wrapper');
    const installmentInput       = document.getElementById('installment_debt');
    const saleNoteInput          = document.getElementById('sale_note');
    const saleNoteRequiredLabel  = document.getElementById('sale-note-required');

    function updateInstallmentUI() {
        if (!paymentSelect) return;
        if (paymentSelect.value === 'installment') {
            installmentWrapper.style.display = 'block';
            if (installmentInput) installmentInput.required = true;
            if (saleNoteInput)    saleNoteInput.required   = true;
            if (saleNoteRequiredLabel) saleNoteRequiredLabel.style.display = 'inline';
        } else {
            installmentWrapper.style.display = 'none';
            if (installmentInput) {
                installmentInput.required = false;
                // không auto xóa giá trị, để anh tự quyết
            }
            if (saleNoteInput)    saleNoteInput.required   = false;
            if (saleNoteRequiredLabel) saleNoteRequiredLabel.style.display = 'none';
        }
    }

    if (paymentSelect) {
        paymentSelect.addEventListener('change', updateInstallmentUI);
        updateInstallmentUI();
    }

    // Khi nhập xong số nợ trả góp, nếu ghi chú đang trống -> auto fill "Nợ trả góp: xxx"
    if (installmentInput && saleNoteInput) {
        installmentInput.addEventListener('blur', function () {
            let raw = (this.value || '').trim();
            if (!raw) return;

            if (!saleNoteInput.value.trim()) {
                saleNoteInput.value = 'Nợ trả góp: ' + raw;
            }
        });
        } // ========== TÍNH NỢ TỰ ĐỘNG ==========

        function parseMoney(str) {
            if (!str) return 0;
            str = String(str).replace(/\D/g, ''); // bỏ hết ký tự không phải số
            if (!str) return 0;
            return parseInt(str, 10) || 0;
        }

        function formatMoney(num) {
            if (!num || isNaN(num)) return '';
            return new Intl.NumberFormat('vi-VN').format(num);
        }

        function calcDebt() {
            let saleInput  = document.getElementById('sale_price');
            let paidInput  = document.getElementById('paid_amount');
            let debtInput  = document.getElementById('debt_amount');

            if (!saleInput || !paidInput || !debtInput) return;

            let sale = parseMoney(saleInput.value);
            let paid = parseMoney(paidInput.value);

            let debt = sale - paid;
            if (debt < 0) debt = 0;

            debtInput.value = debt ? formatMoney(debt) : '';
        }

        // Tự tính khi nhập giá bán / khách trả
        let saleInput  = document.getElementById('sale_price');
        let paidInput  = document.getElementById('paid_amount');

        if (saleInput) {
            saleInput.addEventListener('input', calcDebt);
            saleInput.addEventListener('blur', function () {
                let v = parseMoney(this.value);
                this.value = v ? formatMoney(v) : '';
                calcDebt();
            });
        }
        if (paidInput) {
            paidInput.addEventListener('input', calcDebt);
            paidInput.addEventListener('blur', function () {
                let v = parseMoney(this.value);
                this.value = v ? formatMoney(v) : '';
                calcDebt();
            });
        }

        // Tính lại lần đầu (khi load lại form sau khi validate lỗi)
        calcDebt();




});
</script>
@endsection
