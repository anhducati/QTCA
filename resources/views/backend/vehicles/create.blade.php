@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.vehicles.index') }}">Danh sách xe</a></li>
            <li class="active"><strong>Thêm xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Thêm xe mới</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- brands, models, colors, warehouses, suppliers, importReceiptId --}}
                    <form action="{{ route('admin.vehicles.store') }}" method="POST">
                        @csrf

                        {{-- Nếu vào từ phiếu nhập thì gửi kèm id --}}
                        @if(!empty($importReceiptId))
                            <input type="hidden" name="import_receipt_id" value="{{ $importReceiptId }}">
                        @endif

                        <div class="row">
                            {{-- Hãng xe (chỉ để lọc / hiển thị, không lưu vào bảng vehicles) --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hãng xe <span class="text-danger">*</span></label>
                                    <select id="brand_id" name="brand_id" class="form-control">
                                        <option value="">-- Chọn hãng xe --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Dòng xe --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Dòng xe <span class="text-danger">*</span></label>
                                    <select id="model_id" name="model_id" class="form-control">
                                        <option value="">-- Chọn dòng xe --</option>
                                        @foreach($models as $model)
                                            <option value="{{ $model->id }}"
                                                {{ old('model_id') == $model->id ? 'selected' : '' }}>
                                                {{ $model->name }} ({{ optional($model->brand)->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('model_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Màu xe --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Màu xe</label>
                                    <select id="color_id" name="color_id" class="form-control">
                                        <option value="">-- Chọn màu xe --</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ old('color_id') == $color->id ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('color_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Nhà cung cấp --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nhà cung cấp</label>
                                    <select id="supplier_id" name="supplier_id" class="form-control">
                                        <option value="">-- Chọn nhà cung cấp --</option>
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
                                </div>
                            </div>

                            {{-- Kho --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kho <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" class="form-control">
                                        <option value="">-- Chọn kho --</option>
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
                            </div>
                        </div> {{-- /row --}}

                        <div class="row">
                            {{-- Số khung --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Số khung (VIN) <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="frame_no"
                                           class="form-control"
                                           placeholder="Số khung"
                                           value="{{ old('frame_no') }}">
                                    @error('frame_no')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Số máy --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Số máy</label>
                                    <input type="text"
                                           name="engine_no"
                                           class="form-control"
                                           placeholder="Số máy"
                                           value="{{ old('engine_no') }}">
                                    @error('engine_no')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Đời / Năm --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Đời / Năm</label>
                                    <input type="text"
                                           name="year"
                                           class="form-control"
                                           placeholder="VD: 2024"
                                           value="{{ old('year') }}">
                                    @error('year')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    @php $status = old('status', '0'); @endphp
                                    <select name="status" class="form-control">
                                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Trong kho</option>
                                        <option value="2" {{ $status === '2' ? 'selected' : '' }}>Đặt cọc</option>
                                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Đã bán</option>
                                    </select>
                                    @error('status')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div> {{-- /row --}}

                        <div class="row">
                            {{-- Giá nhập --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Giá nhập (VNĐ)</label>
                                    <input type="text"
                                           name="purchase_price"
                                           class="form-control money-input"
                                           placeholder="VD: 28.000.000"
                                           value="{{ old('purchase_price') }}">
                                    @error('purchase_price')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Biển số --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Biển số (nếu đã đăng ký)</label>
                                    <input type="text"
                                           name="license_plate"
                                           class="form-control"
                                           placeholder="VD: 36B1-123.45"
                                           value="{{ old('license_plate') }}">
                                    @error('license_plate')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Ghi chú --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea name="note"
                                              class="form-control"
                                              rows="2"
                                              placeholder="Ghi chú thêm (sổ bảo hành, smartkey...)">{{ old('note') }}</textarea>
                                    @error('note')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Lưu xe
                            </button>
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-default">
                                Quay lại danh sách
                            </a>
                        </div>

                        {{-- Debug lỗi nếu cần --}}
                        {{-- 
                        @if($errors->any())
                            <pre>{{ print_r($errors->all(), true) }}</pre>
                        @endif 
                        --}}

                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
