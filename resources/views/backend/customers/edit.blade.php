@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Khách hàng</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.customers.index') }}">Danh sách khách hàng</a></li>
            <li class="active"><strong>Cập nhật khách hàng</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cập nhật khách hàng</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- $customer truyền từ CustomerController@edit --}}
                    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $customer->name) }}">
                            @error('name')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone', $customer->phone) }}">
                            @error('phone')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email', $customer->email) }}">
                            @error('email')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>CMND / CCCD</label>
                            <input type="text"
                                   name="id_card"
                                   class="form-control"
                                   value="{{ old('id_card', $customer->id_card) }}">
                            @error('id_card')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   value="{{ old('address', $customer->address) }}">
                            @error('address')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mã số thuế</label>
                            <input type="text"
                                   name="tax_code"
                                   class="form-control"
                                   value="{{ old('tax_code', $customer->tax_code) }}">
                            @error('tax_code')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note"
                                      class="form-control"
                                      rows="3">{{ old('note', $customer->note) }}</textarea>
                            @error('note')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Cập nhật
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
