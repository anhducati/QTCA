@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.vehicles.index') }}">Danh sách xe</a></li>
            <li class="active"><strong>Cập nhật xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cập nhật xe</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- $vehicle, $brands, $models, $colors, $warehouses, $suppliers từ VehicleController@edit --}}
                    <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- HÃNG XE --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Hãng xe <span class="text-danger">*</span></label>
                                    <select id="brand_id" name="brand_id" class="form-control">
                                        <option value="">-- Chọn hãng --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ (int)old('brand_id', $vehicle->brand_id) === $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- DÒNG XE --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Dòng xe <span class="text-danger">*</span></label>
                                    <select id="model_id" name="model_id" class="form-control">
                                        <option value="">-- Chọn dòng xe --</option>
                                        @foreach($models as $model)
                                            <option value="{{ $model->id }}"
                                                {{ (int)old('model_id', $vehicle->model_id) === $model->id ? 'selected' : '' }}>
                                                {{ $model->name }} ({{ optional($model->brand)->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('model_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- MÀU XE --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Màu xe</label>
                                    <select id="color_id" name="color_id" class="form-control">
                                        <option value="">-- Chọn màu --</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}"
                                                {{ (int)old('color_id', $vehicle->color_id) === $color->id ? 'selected' : '' }}>
                                                {{ $color->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('color_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- KHO --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kho</label>
                                    <select id="warehouse_id" name="warehouse_id" class="form-control">
                                        <option value="">-- Chọn kho --</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}"
                                                {{ (int)old('warehouse_id', $vehicle->warehouse_id) === $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div> {{-- row --}}

                        <div class="row">
                            {{-- NHÀ CUNG CẤP --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nhà cung cấp</label>
                                    <select id="supplier_id" name="supplier_id" class="form-control">
                                        <option value="">-- Chọn nhà cung cấp --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ (int)old('supplier_id', $vehicle->supplier_id) === $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- SỐ KHUNG --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Số khung (VIN) <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="frame_no"
                                           class="form-control"
                                           value="{{ old('frame_no', $vehicle->frame_no) }}">
                                    @error('frame_no')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- SỐ MÁY --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Số máy</label>
                                    <input type="text"
                                           name="engine_no"
                                           class="form-control"
                                           value="{{ old('engine_no', $vehicle->engine_no) }}">
                                    @error('engine_no')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- NĂM / ĐỜI --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Đời / Năm</label>
                                    <input type="text"
                                           name="year"
                                           class="form-control"
                                           value="{{ old('year', $vehicle->year) }}">
                                    @error('year')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div> {{-- row --}}

                        <div class="row">
                            {{-- TRẠNG THÁI --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    @php $status = (int)old('status', $vehicle->status); @endphp
                                    <select name="status" class="form-control">
                                        <option value="0" {{ $status === 0 ? 'selected' : '' }}>Trong kho</option>
                                        <option value="1" {{ $status === 1 ? 'selected' : '' }}>Đã bán</option>
                                        <option value="2" {{ $status === 2 ? 'selected' : '' }}>Đặt cọc</option>
                                    </select>
                                    @error('status')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- BIỂN SỐ --}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Biển số (nếu đã đăng ký)</label>
                                    <input type="text"
                                           name="license_plate"
                                           class="form-control"
                                           value="{{ old('license_plate', $vehicle->license_plate) }}">
                                    @error('license_plate')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- GHI CHÚ --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea name="note"
                                              class="form-control"
                                              rows="2">{{ old('note', $vehicle->note) }}</textarea>
                                    @error('note')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-default">
                                Quay lại danh sách
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection

{{-- THÊM PHẦN SLIMSELECT ĐỂ CÓ SEARCH TRONG SELECT --}}
@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.6.0/dist/slimselect.css" rel="stylesheet" />
@endsection

@section('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/slim-select@2.6.0/dist/slimselect.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new SlimSelect({ select: '#brand_id' });
            new SlimSelect({ select: '#model_id' });
            new SlimSelect({ select: '#color_id' });
            new SlimSelect({ select: '#warehouse_id' });
            new SlimSelect({ select: '#supplier_id' });
        });
    </script>
@endsection
