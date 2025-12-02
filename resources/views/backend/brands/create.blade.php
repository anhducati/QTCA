@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Hãng xe</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('admin.brands.index') }}">Danh sách hãng xe</a>
            </li>
            <li class="active">
                <strong>Thêm hãng xe</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Thêm hãng xe mới</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.brands.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Mã hãng <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   placeholder="Ví dụ: YMH, HND..."
                                   value="{{ old('code') }}">
                            @error('code')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Tên hãng <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="Tên hãng xe"
                                   value="{{ old('name') }}">
                            @error('name')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Ghi chú thêm (nếu có)">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Lưu hãng xe
                            </button>
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-default">
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
