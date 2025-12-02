@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Sửa phiếu xuất kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.export_receipts.index') }}">Danh sách phiếu xuất</a></li>
            <li class="active"><strong>Sửa phiếu #{{ $exportReceipt->code }}</strong></li>
        </ol>
    </div>
    <div class="col-lg-2 text-right m-t-lg">
        <a href="{{ route('admin.export_receipts.show', $exportReceipt->id) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">

                <div class="ibox-title">
                    <h5>Thông tin phiếu xuất</h5>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.export_receipts.update', $exportReceipt->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- ===== Thông tin chính ===== --}}
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mã phiếu</label>
                                    <input type="text" class="form-control" value="{{ $exportReceipt->code }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label>Ngày xuất *</label>
                                <input type="date" name="export_date" class="form-control"
                                       value="{{ old('export_date', $exportReceipt->export_date) }}">
                                @error('export_date')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label>Kho xuất *</label>
                                <select name="warehouse_id" class="form-control slim-select">
                                    <option value="">-- Chọn kho --</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}"
                                            {{ old('warehouse_id', $exportReceipt->warehouse_id) == $w->id ? 'selected':'' }}>
                                            {{ $w->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-4">
                                <label>Loại xuất kho *</label>
                                @php $exportType = old('export_type', $exportReceipt->export_type); @endphp
                                <select name="export_type" class="form-control slim-select">
                                    <option value="sell" {{ $exportType=='sell'?'selected':'' }}>Bán buôn</option>
                                    <option value="transfer" {{ $exportType=='transfer'?'selected':'' }}>Chuyển kho</option>
                                    <option value="demo" {{ $exportType=='demo'?'selected':'' }}>Demo / Sự kiện</option>
                                </select>
                                @error('export_type')
                                    <div class="error-danger">* {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- NCC = customer_id --}}
                            <div class="col-md-4">
                                <label>Nhà cung cấp (bán buôn)</label>
                                @php $currentSupplier = old('supplier_id', $exportReceipt->customer_id); @endphp
                                <select name="supplier_id" class="form-control slim-select">
                                    <option value="">-- Chọn NCC --</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}"
                                            {{ $currentSupplier == $s->id ? 'selected':'' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Hạn thanh toán</label>
                                <input type="date" name="due_date" class="form-control"
                                       value="{{ old('due_date', optional($exportReceipt->due_date)->format('Y-m-d')) }}">
                            </div>

                        </div>


                        {{-- ======================= DANH SÁCH XE ======================= --}}
                        <hr>
                        <h4>Danh sách xe trong phiếu</h4>

                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th width="250px">Số khung</th>
                                    <th>Dòng xe</th>
                                    <th>Màu</th>
                                    <th width="150px">Giá bán</th>
                                    <th width="120px">Ghi chú</th>
                                    <th width="70px"></th>
                                </tr>
                            </thead>

                            <tbody>

                            @foreach($exportReceipt->items as $i => $row)
                                @php
                                    $vehicle = $row->vehicle;
                                    $model   = $vehicle->model ?? $row->model;
                                @endphp

                                <tr data-index="{{ $i }}">

                                    <td>
                                        <select name="items[{{ $i }}][vehicle_id]" class="form-control slim-select vehicle-select">
                                            <option value="">-- Chọn xe --</option>
                                            @foreach($vehicles as $v)
                                                <option value="{{ $v->id }}"
                                                    {{ $row->vehicle_id==$v->id?'selected':'' }}>
                                                    {{ $v->frame_no }} - {{ optional($v->model)->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>{{ optional($model)->name }}</td>

                                    <td>{{ optional($vehicle->color)->name }}</td>

                                    <td>
                                        <input type="text"
                                               name="items[{{ $i }}][unit_price]"
                                               class="form-control money-input"
                                               value="{{ $row->unit_price ? number_format($row->unit_price,0,',','.') : '' }}">
                                    </td>

                                    <td>
                                        <input type="text" name="items[{{ $i }}][note]" class="form-control"
                                               value="{{ $row->note }}">
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs remove-row">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                            @endforeach

                            </tbody>
                        </table>

                        <div class="text-right">
                            <button type="button" class="btn btn-success" id="add-item">
                                <i class="fa fa-plus"></i> Thêm xe
                            </button>
                        </div>

                        <hr>

                        <label>Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $exportReceipt->note) }}</textarea>

                        <div class="text-right m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Cập nhật phiếu xuất
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.css">
<script src="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // --- Money Input Formatting ---
    function formatMoneyInput(el) {
        let v = el.value.replace(/\D/g, "");
        el.value = v ? new Intl.NumberFormat("vi-VN").format(v) : "";
    }

    function initMoneyInputs(ctx) {
        ctx.querySelectorAll(".money-input").forEach(function (el) {

            // format initial
            if (el.value) formatMoneyInput(el);

            el.addEventListener("input", () => formatMoneyInput(el));
            el.addEventListener("paste", () => setTimeout(() => formatMoneyInput(el), 0));
        });
    }

    // --- SlimSelect init ---
    function initSlimSelect(ctx) {
        ctx.querySelectorAll("select.slim-select:not([data-ss])").forEach(function (el) {
            new SlimSelect({ select: el, settings: { searchPlaceholder: "Tìm..." } });
            el.setAttribute("data-ss", 1);
        });
    }

    // Init ban đầu
    initSlimSelect(document);
    initMoneyInputs(document);

    // --- Add Row ---
    document.getElementById("add-item").addEventListener("click", function () {
        let tbody = document.querySelector("#items-table tbody");
        let index = tbody.querySelectorAll("tr").length;

        let html = `
            <tr data-index="${index}">
                <td>
                    <select name="items[${index}][vehicle_id]" class="form-control slim-select vehicle-select">
                        <option value="">-- Chọn xe --</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->frame_no }} - {{ optional($v->model)->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td></td>
                <td></td>
                <td>
                    <input type="text" name="items[${index}][unit_price]" class="form-control money-input"
                           placeholder="VD: 30.000.000">
                </td>
                <td>
                    <input type="text" name="items[${index}][note]" class="form-control">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML("beforeend", html);

        let newRow = tbody.lastElementChild;
        initSlimSelect(newRow);
        initMoneyInputs(newRow);
    });

    // --- Remove Row ---
    document.querySelector("#items-table").addEventListener("click", function (e) {
        if (e.target.closest(".remove-row")) {
            e.target.closest("tr").remove();
        }
    });

});
</script>

@endsection
