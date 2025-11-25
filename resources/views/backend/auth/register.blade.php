<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập Admin</title>

    <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-3">

        </div>

        <div class="col-md-6">
            <div class="ibox-content">

                <form class="m-t" role="form" action="" method="post" >
                    @csrf
                    <div class="form-group">
                        <input type="text"
                               class="form-control"
                               placeholder="Email"
                               name="name"
                               value="{{ old('email') }}"
                        >
                        @error('name')
                        <div class="error-danger">* {{ $message }}</div>
                        @enderror
                    </div>
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
                    <div class="form-group">
                        <input type="password"
                               class="form-control"
                               placeholder="Mật khẩu"
                               name="confirm-password"
                        >
                        @error('confirm-password')
                        <div class="error-danger">* {{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Đăng ký</button>

                    <a href="#">
                        <small>Quên mật khẩu?</small>
                    </a>


                </form>
                <p class="m-t">
                    <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small>
                </p>
            </div>
        </div>

        <div class="col-md-3">

        </div>
    </div>
    <hr/>
    AR
</div>

</body>

</html>

