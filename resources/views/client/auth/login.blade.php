<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chung Anh</title>
    <link rel="icon" href="{{asset('assets/client/img/logo_TLU.ico')}}">

    <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/backend/css/animate.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('assets/backend/css/style.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/client/css/customs.css') }}" />
    <link href="{{ asset('assets/client/css/style3.css') }}" rel="stylesheet">

</head>
<style>
    /* body {
        background-image: url('{{ asset('assets/client/img/dhthuyloi1.jpg') }}');
        background-size: cover;
        background-repeat: no-repeat;
    } */


</style>
<body class="gray-bg">

<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-3">

        </div>

        <div class="col-md-6">

            <div class="ibox-content">
                <h3 class="text-center mb-5">
                    Đăng nhập
                </h3>
                <hr>

                @include('layouts.message')

                <form class="m-t" role="form" action="" method="post" >
                    @csrf
                    <div class="form-group">
                        <input type="text"
                               class="form-control"
                               placeholder="Email"
                               name="email"
                               value="{{ old('email') }}"
                        >
                        @error('email')
                        <div class="error-danger">* {{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password"
                               class="form-control"
                               placeholder="Mật khẩu"
                               name="password"
                        >
                        @error('password')
                        <div class="error-danger">* {{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">
                        Đăng nhập
                    </button>

                    <a href="{{ route('client.forgot') }}">
                        <p class="text-center">Quên mật khẩu?</p>
                    </a>


                </form>
               


            </div>
        </div>

        <div class="col-md-3">

        </div>
    </div>


</div>

</body>

</html>

