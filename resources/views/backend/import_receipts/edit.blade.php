@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Phiếu nhập kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.import_receipts.index') }}">Danh sách phiếu nhập</a></li>
            <li class="active"><strong>Cập nhật phiếu nhập</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cập nhật phiếu nhập kho</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.import_receipts.update', $importReceipt->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mã phiếu</label>
                                    <input type="text"
                                           name="code"
                                           class="form-control"
                                           value="{{ old('code', $importReceipt->code) }}">
                                    @error('code')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ngày nhập <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="import_date"
                                           class="form-control"
                                           value="{{ old('import_date', $importReceipt->import_date) }}">
                                    @error('import_date')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kho nhập <span class="text-danger">*</span></label>
                                    <select id="warehouse_id" name="warehouse_id" class="form-control">
                                        <option value="">-- Chọn kho --</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}"
                                                {{ (int)old('warehouse_id', $importReceipt->warehouse_id) === $warehouse->id ? 'selected' : '' }}>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nhà cung cấp <span class="text-danger">*</span></label>
                                    <select id="supplier_id" name="supplier_id" class="form-control">
                                        <option value="">-- Chọn nhà cung cấp --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ (int)old('supplier_id', $importReceipt->supplier_id) === $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ghi chú</label>
                                    <textarea name="note"
                                              class="form-control"
                                              rows="2">{{ old('note', $importReceipt->note) }}</textarea>
                                    @error('note')
                                    <div class="error-danger">* {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Cập nhật phiếu
                            </button>
                            <a href="{{ route('admin.import_receipts.index') }}" class="btn btn-default">
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

@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.6.0/dist/slimselect.css" rel="stylesheet" />
@endsection

@section('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/slim-select@2.6.0/dist/slimselect.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new SlimSelect({ select: '#warehouse_id' });
            new SlimSelect({ select: '#supplier_id' });
        });
    </script>
@endsection
