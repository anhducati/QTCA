@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Khách hàng</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.customers.index') }}">Danh sách khách hàng</a></li>
            <li class="active"><strong>Thêm khách hàng</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Thêm khách hàng mới</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.customers.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="Họ tên khách hàng"
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
                                   placeholder="Địa chỉ email"
                                   value="{{ old('email') }}">
                            @error('email')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>CMND / CCCD</label>
                            <input type="text"
                                   name="id_card"
                                   class="form-control"
                                   placeholder="Số CMND/CCCD"
                                   value="{{ old('id_card') }}">
                            @error('id_card')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   placeholder="Địa chỉ liên hệ"
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
                                   placeholder="Mã số thuế (nếu có)"
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
                                <i class="fa fa-save"></i> Lưu khách hàng
                            </button>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-default">
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
