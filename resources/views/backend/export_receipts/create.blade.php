@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Tạo phiếu xuất kho bán buôn</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.export_receipts.index') }}">Danh sách phiếu xuất</a></li>
            <li class="active"><strong>Tạo phiếu xuất</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.export_receipts.index') }}" class="btn btn-default">
            <i class="fa fa-list"></i> Danh sách phiếu xuất
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')
        </div>
    </div>

    <form action="{{ route('admin.export_receipts.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- THÔNG TIN CHUNG PHIẾU XUẤT --}}
            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin phiếu xuất</h5>
                    </div>
                    <div class="ibox-content">

                        {{-- Ngày xuất --}}
                        <div class="form-group">
                            <label>Ngày xuất <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="export_date"
                                   class="form-control"
                                   value="{{ old('export_date', now()->toDateString()) }}">
                            @error('export_date')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kho xuất --}}
                        <div class="form-group">
                            <label>Kho xuất <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-control">
                                <option value="">-- Chọn kho xuất --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}"
                                        {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Loại xuất kho --}}
                        <div class="form-group">
                            <label>Loại xuất kho <span class="text-danger">*</span></label>
                            @php
                                $exportType = old('export_type', 'sell');
                            @endphp
                            <select name="export_type" id="export_type" class="form-control">
                                <option value="sell" {{ $exportType === 'sell' ? 'selected' : '' }}>
                                    Bán buôn (xuất cho đối tác)
                                </option>
                                <option value="transfer" {{ $exportType === 'transfer' ? 'selected' : '' }}>
                                    Chuyển kho nội bộ
                                </option>
                                <option value="demo" {{ $exportType === 'demo' ? 'selected' : '' }}>
                                    Xe demo / sự kiện
                                </option>
                            </select>
                            @error('export_type')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                            <p class="text-muted m-t-xs">
                                * Nếu chọn <strong>Chuyển kho</strong> thì không cần chọn khách hàng.
                            </p>
                        </div>

                        {{-- "Khách hàng" = Nhà cung cấp / đối tác --}}
                        <div class="form-group" id="supplier_wrapper">
                            <label>Đối tác nhận xe (Nhà cung cấp)</label>
                            <select name="supplier_id" class="form-control">
                                <option value="">-- Chọn đối tác / nhà cung cấp --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                            <p class="text-muted m-t-xs">
                                * Bắt buộc khi loại xuất kho là <strong>Bán buôn</strong>.
                            </p>
                        </div>

                        {{-- Hạn thanh toán --}}
                        <div class="form-group">
                            <label>Hạn thanh toán</label>
                            <input type="date"
                                   name="due_date"
                                   class="form-control"
                                   value="{{ old('due_date') }}">
                            @error('due_date')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ghi chú --}}
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note"
                                      rows="3"
                                      class="form-control"
                                      placeholder="Ghi chú thêm...">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- DANH SÁCH XE XUẤT --}}
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Danh sách xe xuất kho</h5>
                    </div>
                    <div class="ibox-content">

                        <p class="text-muted">
                            Chọn các xe đang trong kho để xuất bán buôn / chuyển kho.  
                            Giá bán có thể nhập theo từng xe. Thành tiền = Giá bán.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="export-items-table">
                                <thead>
                                <tr>
                                    <th style="width: 30%;">Xe (tìm nhanh theo số khung / dòng / màu / kho)</th>
                                    <th style="width: 20%;">Giá bán (VNĐ)</th>
                                    <th style="width: 20%;">Thành tiền</th>
                                    <th>Ghi chú</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                                </thead>
                                <tbody id="export-items-body">
                                @php $oldItems = old('items', [ [] ]); @endphp
                                @foreach($oldItems as $idx => $oldItem)
                                    <tr class="export-item-row" data-index="{{ $idx }}">
                                        {{-- XE --}}
                                        <td>
                                            <select name="items[{{ $idx }}][vehicle_id]"
                                                    class="form-control select-vehicle js-vehicle-select">
                                                <option value="">-- Chọn xe --</option>
                                                @foreach($vehiclesInStock as $vehicle)
                                                    @php
                                                        $label = $vehicle->frame_no
                                                            . ' - ' . optional($vehicle->model)->name
                                                            . ' - ' . optional($vehicle->color)->name
                                                            . ' - Kho: ' . optional($vehicle->warehouse)->name;
                                                    @endphp
                                                    <option value="{{ $vehicle->id }}"
                                                        data-price="{{ $vehicle->purchase_price ?? 0 }}"
                                                        {{ ($oldItem['vehicle_id'] ?? null) == $vehicle->id ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("items.$idx.vehicle_id")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- GIÁ BÁN --}}
                                        <td>
                                            <input type="text"
                                                   name="items[{{ $idx }}][unit_price]"
                                                   class="form-control money-input unit-price-input"
                                                   placeholder="VD: 28.000.000"
                                                   value="{{ $oldItem['unit_price'] ?? '' }}">
                                            @error("items.$idx.unit_price")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- THÀNH TIỀN --}}
                                        <td>
                                            <input type="text"
                                                   class="form-control amount-display"
                                                   placeholder="Tự tính"
                                                   readonly>
                                        </td>

                                        {{-- GHI CHÚ --}}
                                        <td>
                                            <input type="text"
                                                   name="items[{{ $idx }}][note]"
                                                   class="form-control"
                                                   placeholder="Ghi chú"
                                                   value="{{ $oldItem['note'] ?? '' }}">
                                            @error("items.$idx.note")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </td>

                                        {{-- XÓA --}}
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-xs btn-danger btn-remove-row"
                                                    style="{{ $idx == 0 ? 'display:none;' : '' }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right">
                                        <button type="button" id="btn-add-row" class="btn btn-success btn-sm">
                                            <i class="fa fa-plus"></i> Thêm dòng xe
                                        </button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- TỔNG TIỀN TẠM TÍNH --}}
                        <div class="text-right m-t-sm">
                            <strong>Tổng tạm tính:</strong>
                            <span id="total-amount-display">0</span> <span>VNĐ</span>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Lưu phiếu xuất
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </form>
</div>

@endsection

@section('page-scripts')
    {{-- SlimSelect --}}
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.8.1/dist/slimselect.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/slim-select@2.8.1/dist/slimselect.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ===== Ẩn/hiện đối tác nhận xe theo loại xuất =====
        var exportTypeSelect = document.getElementById('export_type');
        var supplierWrapper = document.getElementById('supplier_wrapper');

        function toggleSupplier() {
            var type = exportTypeSelect.value;
            if (type === 'sell') {
                supplierWrapper.style.opacity = '1';
                supplierWrapper.style.pointerEvents = 'auto';
            } else {
                supplierWrapper.style.opacity = '0.5';
                supplierWrapper.style.pointerEvents = 'none';
            }
        }
        exportTypeSelect.addEventListener('change', toggleSupplier);
        toggleSupplier();

        // ===== KHỞI TẠO SLIMSELECT =====
        function initSlimSelect() {
            var selects = document.querySelectorAll('select.js-vehicle-select');
            selects.forEach(function (el) {
                if (el.dataset.slimInit === '1') return; // tránh khởi tạo 2 lần

                new SlimSelect({
                    select: el,
                    settings: {
                        placeholderText: 'Tìm theo số khung / dòng / màu / kho...',
                        searchText: 'Không tìm thấy kết quả',
                        searchPlaceholder: 'Gõ để tìm...',
                        allowDeselect: true,
                        closeOnSelect: true,
                    }
                });

                el.dataset.slimInit = '1';
            });
        }
        initSlimSelect();

        // ===== HÀM XỬ LÝ TIỀN =====
        function parseVN(str) {
            if (!str) return 0;
            return parseInt(String(str).replace(/[^\d]/g, ''), 10) || 0;
        }

        function formatVN(num) {
            num = parseInt(num || 0, 10);
            if (isNaN(num)) num = 0;
            return num.toLocaleString('vi-VN');
        }

        function recalcRow(row) {
            var unitInput = row.querySelector('.unit-price-input');
            var amountInput = row.querySelector('.amount-display');
            var unit = parseVN(unitInput.value);
            amountInput.value = unit ? formatVN(unit) : '';
            return unit;
        }

        function recalcTotal() {
            var total = 0;
            document.querySelectorAll('#export-items-body .export-item-row').forEach(function (row) {
                var amountInput = row.querySelector('.amount-display');
                total += parseVN(amountInput.value);
            });
            document.getElementById('total-amount-display').textContent = formatVN(total);
        }

        // Khi chọn xe -> autofill giá nếu ô giá trống
        document.getElementById('export-items-body').addEventListener('change', function (e) {
            if (!e.target.closest('.select-vehicle')) return;
            var selectEl = e.target;
            var row = selectEl.closest('.export-item-row');
            var selectedOption = selectEl.options[selectEl.selectedIndex];
            var price = selectedOption ? parseInt(selectedOption.getAttribute('data-price') || '0', 10) : 0;
            var unitInput = row.querySelector('.unit-price-input');

            if (unitInput && !unitInput.value) {
                unitInput.value = price ? formatVN(price) : '';
            }

            recalcRow(row);
            recalcTotal();
        });

        // Format tiền khi blur
        document.getElementById('export-items-body').addEventListener('blur', function (e) {
            if (!e.target.classList.contains('money-input')) return;
            var row = e.target.closest('.export-item-row');
            var val = parseVN(e.target.value);
            e.target.value = val ? formatVN(val) : '';
            recalcRow(row);
            recalcTotal();
        }, true);

        // XÓA DÒNG
        document.getElementById('export-items-body').addEventListener('click', function (e) {
            if (!e.target.closest('.btn-remove-row')) return;
            var row = e.target.closest('.export-item-row');
            row.parentNode.removeChild(row);

            // Đảm bảo dòng đầu tiên không có nút xóa
            var rows = document.querySelectorAll('#export-items-body .export-item-row');
            rows.forEach(function (r, idx) {
                var btn = r.querySelector('.btn-remove-row');
                if (btn) {
                    btn.style.display = (idx === 0 ? 'none' : 'inline-block');
                }
            });

            recalcTotal();
        });

        // THÊM DÒNG
        document.getElementById('btn-add-row').addEventListener('click', function () {
            var body = document.getElementById('export-items-body');
            var index = body.querySelectorAll('.export-item-row').length;

            var optionsHtml = '<option value=\"\">-- Chọn xe --</option>';
            @foreach($vehiclesInStock as $vehicle)
                optionsHtml += `<option value="{{ $vehicle->id }}"
                    data-price="{{ $vehicle->purchase_price ?? 0 }}">
                    {{ $vehicle->frame_no }} - {{ optional($vehicle->model)->name }} - {{ optional($vehicle->color)->name }} - Kho: {{ optional($vehicle->warehouse)->name }}
                </option>`;
            @endforeach

            var rowHtml = document.createElement('tr');
            rowHtml.className = 'export-item-row';
            rowHtml.setAttribute('data-index', index);
            rowHtml.innerHTML = `
                <td>
                    <select name="items[${index}][vehicle_id]"
                            class="form-control select-vehicle js-vehicle-select">
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="text"
                           name="items[${index}][unit_price]"
                           class="form-control money-input unit-price-input"
                           placeholder="VD: 28.000.000">
                </td>
                <td>
                    <input type="text"
                           class="form-control amount-display"
                           placeholder="Tự tính"
                           readonly>
                </td>
                <td>
                    <input type="text"
                           name="items[${index}][note]"
                           class="form-control"
                           placeholder="Ghi chú">
                </td>
                <td class="text-center">
                    <button type="button"
                            class="btn btn-xs btn-danger btn-remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;

            body.appendChild(rowHtml);

            // Khởi tạo SlimSelect cho select mới
            initSlimSelect();
        });

        // Tính tổng lần đầu nếu có old()
        document.querySelectorAll('#export-items-body .export-item-row').forEach(function (row) {
            recalcRow(row);
        });
        recalcTotal();
    });
    </script>
@endsection
