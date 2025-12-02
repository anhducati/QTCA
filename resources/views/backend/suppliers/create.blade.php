@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Nhà cung cấp</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.suppliers.index') }}">Danh sách nhà cung cấp</a></li>
            <li class="active"><strong>Thêm nhà cung cấp</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-8">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Thêm nhà cung cấp mới</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.suppliers.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Tên nhà cung cấp <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="Tên nhà cung cấp"
                                   value="{{ old('name') }}">
                            @error('name')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   placeholder="Số điện thoại"
                                   value="{{ old('phone') }}">
                            @error('phone')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text"
                                   name="email"
                                   class="form-control"
                                   placeholder="Email"
                                   value="{{ old('email') }}">
                            @error('email')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   placeholder="Địa chỉ"
                                   value="{{ old('address') }}">
                            @error('address')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mã số thuế</label>
                            <input type="text"
                                   name="tax_code"
                                   class="form-control"
                                   placeholder="Mã số thuế"
                                   value="{{ old('tax_code') }}">
                            @error('tax_code')
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
                                <i class="fa fa-save"></i> Lưu nhà cung cấp
                            </button>
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-default">
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
