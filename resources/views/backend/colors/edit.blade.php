@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Màu xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.colors.index') }}">Danh sách màu xe</a></li>
            <li class="active"><strong>Cập nhật màu xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-8">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Cập nhật màu xe</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- $color từ ColorController@edit --}}
                    <form action="{{ route('admin.colors.update', $color->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Mã màu <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="code"
                                   class="form-control"
                                   value="{{ old('code', $color->code) }}">
                            @error('code')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Tên màu <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ old('name', $color->name) }}">
                            @error('name')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mã màu HEX (tùy chọn)</label>
                            <input type="text"
                                   name="hex_code"
                                   class="form-control"
                                   value="{{ old('hex_code', $color->hex_code) }}">
                            @error('hex_code')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3">{{ old('note', $color->note) }}</textarea>
                            @error('note')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="m-t-md">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.colors.index') }}" class="btn btn-default">
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
