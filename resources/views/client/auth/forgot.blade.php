<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quân mật khẩu</title>
    <link rel="icon" href="{{asset('assets/client/img/logo_TLU.ico')}}">

    <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/css/style3.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-3">

        </div>

        <div class="col-md-6">

            <div class="ibox-content">
                <h3 class="page-heading text-center">Quên mật khẩu</h3>
                @include('layouts.message')
                <form class="m-t" role="form" action="" method="post" >
                    @csrf
                    @if(session('msg-success'))

                        <a href="{{ route('client.login') }}" class="btn btn-primary block full-width m-b">Đến trang đăng nhập</a>
                    @else

                        <div class="form-group">
                            <input type="text"
                                   class="form-control"
                                   placeholder="Email đăng ký"
                                   name="email"
                                   value="{{ old('email') }}"
                            >
                            @error('email')
                            <div class="error-danger">* {{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary block full-width m-b">Lấy lại mật khẩu</button>

                    @endif


                    <a href="{{ route('client.home') }}">
                        <small class="btn btn-outline-info">Trở lại trang chủ</small>
                    </a>


                </form>

            </div>
        </div>

    </div>

</div>

</body>

</html>


