<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đổi mật khẩu</title>

    <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/css/style3.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">
    <link rel="icon" href="{{asset('assets/client/img/logo_TLU.ico')}}">

</head>

<body class="gray-bg">

<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-3">

        </div>

        <div class="col-md-6">

            <div class="ibox-content">
                <h3 class="page-heading text-center">Thay đổi mật khẩu</h3>
                @include('layouts.message')
                <form class="m-t" role="form" action="" method="post" >
                    @csrf
                    <div class="form-group">
                        <input type="password"
                               class="form-control"
                               placeholder="Mật khẩu mới"
                               name="password"
                               value="{{ old('password') }}"
                        >
                        @error('password')
                        <div class="error-danger">* {{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password"
                               class="form-control"
                               placeholder="Xác nhận mật khẩu mới"
                               name="confirm-password"
                               value="{{ old('confirm-password') }}"
                        >
                        @error('confirm-password')
                        <div class="error-danger">* {{ $message }}</div>
                        @enderror
                    </div>


                    <button type="submit" class="btn btn-primary block full-width m-b">Lấy lại mật khẩu</button>

                    <a href="{{ route('client.home') }}" class="btn btn-warning"> Trở lại</a>


                </form>

            </div>
        </div>

        <div class="col-md-3">

        </div>
    </div>

</div>

</body>

</html>



