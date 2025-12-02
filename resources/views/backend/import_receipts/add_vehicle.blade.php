@extends('layouts.panel')

@section('main')

{{-- PAGE HEADER --}}
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Thêm xe vào phiếu nhập</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.import_receipts.index') }}">Danh sách phiếu nhập</a></li>
            <li>
                <a href="{{ route('admin.import_receipts.show', $importReceipt->id) }}">
                    Phiếu nhập #{{ $importReceipt->code }}
                </a>
            </li>
            <li class="active"><strong>Thêm xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.import_receipts.show', $importReceipt->id) }}"
           class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại phiếu nhập
        </a>
    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">

                <div class="ibox-title">
                    <h5>Thêm xe cho phiếu nhập {{ $importReceipt->code }}</h5>
                    <div class="ibox-tools">
                        <span class="label label-info">
                            Ngày nhập: {{ \Carbon\Carbon::parse($importReceipt->import_date)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- INFO --}}
                    <div class="alert alert-info m-b-md">
                        <p>
                            <strong>Nhà cung cấp:</strong>
                            {{ optional($importReceipt->supplier)->name ?? 'Không xác định' }}
                        </p>
                        <p class="m-b-none">
                            <strong>Kho nhập:</strong>
                            {{ optional($importReceipt->warehouse)->name }}
                        </p>
                    </div>

                    {{-- FORM --}}
                    <form action="{{ route('admin.import_receipts.vehicles.store', $importReceipt->id) }}" method="POST">
                        @csrf

                        @php
                            $oldVehicles = old('vehicles', [ [] ]);
                        @endphp

                        <div id="vehicles-wrapper">
                        @foreach($oldVehicles as $idx => $oldVehicle)
                        <div class="panel panel-default vehicle-item m-b-md" data-index="{{ $idx }}">
                            <div class="panel-heading d-flex justify-content-between"
                                style="display:flex;align-items:center;justify-content:space-between;">
                                <strong>Xe #{{ $idx + 1 }}</strong>
                                <button type="button" class="btn btn-xs btn-danger btn-remove-vehicle"
                                        style="{{ $idx == 0 ? 'display:none;' : '' }}">
                                    <i class="fa fa-trash"></i> Xóa xe này
                                </button>
                            </div>

                            <div class="panel-body">

                                {{-- Row 1 --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Hãng xe <span class="text-danger">*</span></label>
                                            <select name="vehicles[{{ $idx }}][brand_id]" class="form-control slim-select">
                                                <option value="">-- Chọn hãng xe --</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        {{ ($oldVehicle['brand_id'] ?? null) == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("vehicles.$idx.brand_id")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Model --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Dòng xe <span class="text-danger">*</span></label>
                                            <select name="vehicles[{{ $idx }}][model_id]" class="form-control slim-select">
                                                <option value="">-- Chọn dòng xe --</option>
                                                @foreach($models as $model)
                                                    <option value="{{ $model->id }}"
                                                        {{ ($oldVehicle['model_id'] ?? null) == $model->id ? 'selected' : '' }}>
                                                        {{ $model->name }} ({{ optional($model->brand)->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("vehicles.$idx.model_id")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Color --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Màu xe</label>
                                            <select name="vehicles[{{ $idx }}][color_id]" class="form-control slim-select">
                                                <option value="">-- Chọn màu xe --</option>
                                                @foreach($colors as $color)
                                                    <option value="{{ $color->id }}"
                                                        {{ ($oldVehicle['color_id'] ?? null) == $color->id ? 'selected' : '' }}>
                                                        {{ $color->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("vehicles.$idx.color_id")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Warehouse --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Kho</label>
                                            <select name="vehicles[{{ $idx }}][warehouse_id]" class="form-control slim-select">
                                                <option value="">-- Chọn kho --</option>
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ ($oldVehicle['warehouse_id'] ?? $importReceipt->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error("vehicles.$idx.warehouse_id")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Row 2 --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Số khung (VIN) <span class="text-danger">*</span></label>
                                            <input type="text" name="vehicles[{{ $idx }}][frame_no]"
                                                class="form-control" required
                                                placeholder="Số khung"
                                                value="{{ $oldVehicle['frame_no'] ?? '' }}">
                                            @error("vehicles.$idx.frame_no")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Số máy</label>
                                            <input type="text" name="vehicles[{{ $idx }}][engine_no]"
                                                class="form-control"
                                                placeholder="Số máy"
                                                value="{{ $oldVehicle['engine_no'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Đời / Năm</label>
                                            <input type="text" name="vehicles[{{ $idx }}][year]"
                                                class="form-control"
                                                placeholder="VD: 2024"
                                                value="{{ $oldVehicle['year'] ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Giá nhập (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="vehicles[{{ $idx }}][purchase_price]"
                                                   class="form-control money-input"
                                                   placeholder="VD: 28.000.000"
                                                   value="{{ isset($oldVehicle['purchase_price'])
                                                       ? number_format((int) preg_replace('/\D/', '', $oldVehicle['purchase_price']), 0, ',', '.')
                                                       : '' }}">
                                            @error("vehicles.$idx.purchase_price")
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Trạng thái</label>
                                            @php $status = $oldVehicle['status'] ?? 0; @endphp
                                            <select name="vehicles[{{ $idx }}][status]" class="form-control slim-select">
                                                <option value="0" {{ $status == 0 ? 'selected' : '' }}>Trong kho</option>
                                                <option value="2" {{ $status == 2 ? 'selected' : '' }}>Đặt cọc</option>
                                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Đã bán</option>
                                            </select>
                                        </div>
                                    </div>

                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Biển số</label>
                                            <input type="text"
                                                   name="vehicles[{{ $idx }}][license_plate]"
                                                   class="form-control"
                                                   placeholder="VD: 36B1-123.45"
                                                   value="{{ $oldVehicle['license_plate'] ?? '' }}">
                                        </div>
                                    </div>

                                </div>

                                {{-- Row 3 --}}
                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Ghi chú</label>
                                            <textarea name="vehicles[{{ $idx }}][note]"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Ghi chú">{{ $oldVehicle['note'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endforeach
                        </div>

                        <div class="m-t-md text-right">
                            <button type="button" id="add-vehicle" class="btn btn-success">
                                <i class="fa fa-plus"></i> Thêm xe
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check"></i> Xác nhận
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- TEMPLATE CLONE --}}
<script type="text/template" id="vehicle-template">
<div class="panel panel-default vehicle-item m-b-md" data-index="__INDEX__">
    <div class="panel-heading" style="display:flex;align-items:center;justify-content:space-between;">
        <strong>Xe #__NUMBER__</strong>
        <button type="button" class="btn btn-xs btn-danger btn-remove-vehicle">
            <i class="fa fa-trash"></i> Xóa xe này
        </button>
    </div>

    <div class="panel-body">

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Hãng xe <span class="text-danger">*</span></label>
                    <select name="vehicles[__INDEX__][brand_id]" class="form-control slim-select">
                        <option value="">-- Chọn hãng xe --</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Dòng xe <span class="text-danger">*</span></label>
                    <select name="vehicles[__INDEX__][model_id]" class="form-control slim-select">
                        <option value="">-- Chọn dòng xe --</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">
                                {{ $model->name }} ({{ optional($model->brand)->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Màu xe</label>
                    <select name="vehicles[__INDEX__][color_id]" class="form-control slim-select">
                        <option value="">-- Chọn màu xe --</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Kho</label>
                    <select name="vehicles[__INDEX__][warehouse_id]" class="form-control slim-select">
                        <option value="">-- Chọn kho --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ $warehouse->id == $importReceipt->warehouse_id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Số khung <span class="text-danger">*</span></label>
                    <input type="text" required
                        name="vehicles[__INDEX__][frame_no]"
                        class="form-control"
                        placeholder="Số khung">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Số máy</label>
                    <input type="text"
                        name="vehicles[__INDEX__][engine_no]"
                        class="form-control"
                        placeholder="Số máy">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Đời</label>
                    <input type="text"
                        name="vehicles[__INDEX__][year]"
                        class="form-control"
                        placeholder="VD: 2024">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Giá nhập <span class="text-danger">*</span></label>
                    <input type="text" required
                        name="vehicles[__INDEX__][purchase_price]"
                        class="form-control money-input"
                        placeholder="VD: 28.000.000">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="vehicles[__INDEX__][status]" class="form-control slim-select">
                        <option value="0" selected>Trong kho</option>
                        <option value="2">Đặt cọc</option>
                        <option value="1">Đã bán</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Biển số</label>
                    <input type="text"
                        name="vehicles[__INDEX__][license_plate]"
                        class="form-control"
                        placeholder="VD: 36B1-123.45">
                </div>
            </div>


        </div>

        <div class="row">


            <div class="col-md-8">
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="vehicles[__INDEX__][note]" class="form-control" rows="2"></textarea>
                </div>
            </div>

        </div>
    </div>
</div>
</script>


@endsection


@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var wrapper   = document.getElementById('vehicles-wrapper');
    var addBtn    = document.getElementById('add-vehicle');
    var templateHtml = document.getElementById('vehicle-template').innerHTML;

    function refreshTitles() {
        wrapper.querySelectorAll('.vehicle-item').forEach((item, idx) => {
            item.setAttribute('data-index', idx);
            var title = item.querySelector('.panel-heading strong');
            if (title) title.textContent = 'Xe #' + (idx + 1);

            var removeBtn = item.querySelector('.btn-remove-vehicle');
            if (removeBtn) removeBtn.style.display = idx === 0 ? 'none' : 'inline-block';
        });
    }

    // GỌI LẠI FORMAT TIỀN CHO FORM BAN ĐẦU (phòng khi layout chưa chạy kịp)
    if (window.CA_FormatMoney_ApplyTo) {
        window.CA_FormatMoney_ApplyTo(wrapper);
    }

    // Add row
    addBtn.addEventListener('click', () => {
        var count = wrapper.querySelectorAll('.vehicle-item').length;
        var html  = templateHtml
                        .replace(/__INDEX__/g, count)
                        .replace(/__NUMBER__/g, count + 1);

        var div = document.createElement('div');
        div.innerHTML = html.trim();
        var newItem = div.firstChild;

        wrapper.appendChild(newItem);

        // Init SlimSelect cho các <select> mới
        if (window.CA_InitSlimSelect) {
            window.CA_InitSlimSelect(newItem);
        }

        // Init formatter tiền cho input mới
        if (window.CA_FormatMoney_ApplyTo) {
            window.CA_FormatMoney_ApplyTo(newItem);
        }

        refreshTitles();
    });

    // Remove row
    wrapper.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-vehicle')) {
            var item = e.target.closest('.vehicle-item');
            if (item) item.remove();
            refreshTitles();
        }
    });

    refreshTitles();
});
</script>
@endsection
