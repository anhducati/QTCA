@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Dòng xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.models.index') }}">Danh sách dòng xe</a></li>
            <li class="active"><strong>Cập nhật dòng xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cập nhật dòng xe</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- $model, $brands được truyền từ VehicleModelController@edit --}}
                    <form action="{{ route('admin.models.update', $model->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Hãng xe <span class="text-danger">*</span></label>
                            <select name="brand_id" class="form-control">
                                <option value="">-- Chọn hãng xe --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ (int)old('brand_id', $model->brand_id) === $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mã dòng <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   value="{{ old('code', $model->code) }}">
                            @error('code')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Tên dòng <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $model->name) }}">
                            @error('name')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Năm</label>
                            <input type="text"
                                   name="year"
                                   class="form-control"
                                   value="{{ old('year', $model->year) }}">
                            @error('year')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3">{{ old('note', $model->note) }}</textarea>
                            @error('note')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.models.index') }}" class="btn btn-default">
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
