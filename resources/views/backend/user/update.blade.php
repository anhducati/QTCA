@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.user.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.user.updateTitle') }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{config('apps.user.updateTitle') }}</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12 b-r">
                                @include('layouts.message')
                                <form role="form" method="post" action="">
                                    @method('PUT')
                                    @csrf
                                    <div class="form-group">
                                        <label>Họ tên</label>
                                        <input type="text" name="name"
                                               placeholder="Nhập họ tên"
                                               class="form-control"
                                               value="{{ old('name', $user->name) }}"
                                        >
                                        @error('name')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" name="email"
                                               placeholder="Nhập email" class="form-control"
                                               value="{{ old('email', $user->email) }}">
                                        @error('email')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Vai trò</label>
                                        <select name="is_admin" id="" class="form-control" value="{{ old('is_admin') }}">
                                            @if($user->is_admin == 1)
                                                <option value="1" selected>Quản trị viên</option>
                                            @else
                                                <option value="1" >Quản trị viên</option>
                                            @endif

                                            @if($user->is_admin == 0)
                                                <option value="0" selected>Người dùng</option>
                                            @else
                                                <option value="0">Người dùng</option>
                                            @endif

                                        </select>
                                        @error('is_admin')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select name="status" id="" class="form-control">
                                            <option value="1" {{ ($user->status == 1) ? 'selected' : '' }} >
                                                Kích hoạt
                                            </option>
                                            <option value="0" {{ ($user->status == 0) ? 'selected' : '' }} >Không kích hoạt</option>
                                        </select>
                                        @error('status')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            Mật khẩu mới (<span class="text-danger">Nhập mật khẩu mới nếu muốn thay đổi</span>)
                                        </label>
                                        <input type="password" name="password"
                                               placeholder="Nhập mật khẩu" class="form-control"
                                               value="{{ old('password') }}"
                                        >
                                        @error('password')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror

                                    </div>


                                    <div>
                                        <button class="btn btn-primary" type="submit">
                                            <strong>Cập nhật tài khoản</strong>
                                        </button>
                                        <a class="btn btn-danger" href="{{ route('admin.user.index') }}">Danh sách tài khoản</a>

                                    </div>
                                </form>
                            </div>s

                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>


    

@endsection

@section('page-css')
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">


    {{--    summernote    --}}
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

@endsection

@section('page-scripts')
    {{--    summernote    --}}
    <script src="{{ asset('assets/backend/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>



    <script>
        $(document).ready(function(){

            $('.summernote').summernote();

        });



    </script>
@endsection

