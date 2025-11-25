<!DOCTYPE html><html lang="en"><head>
	<meta charset="UTF-8">
	<title>Trường đại học Thủy Lợi</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="/images/icon_tlu.ico">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
  <!-- <link rel="stylesheet" href="css/normalize.min.css"> -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700,300">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="{{ asset('assets/client/css/login-daihoc.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/client/css/customs.css') }}" />
	
	
</head>
<body >

<div class="signup__container" >
 
  <div class="container__child signup__thumbnail">
    <div class="signup__overlay">
      <a href="{{ route('client.home') }}">
        <img src="{{ asset('assets/client/css/60.png') }}" style="margin-top: 160px; margin-left: 100px;" width="50%" alt="">
      </a>
    </div>
  </div>
  <div class="container__child signup__form" >
    <form class="m-t" role="form" action="" method="post" >
       @csrf
      <div class="form-group" style="margin-top:85px">
        <label for="email">Email</label>
        <input class="form-control" type="text" name="email" id="email"
        value="{{ old('email') }}" placeholder="Nhập email" required="" >

         @error('email')
            <div style="color: red; font-size: 14px;" class="error-danger">* {{ $message }}</div>
        @enderror
        
      </div>
      
      <div class="form-group">
        <label for="password" style="margin-top:25px">Mật khẩu</label>
        <input class="form-control" type="password" name="password" 
        id="password" placeholder="********" required="" >
        @error('password')
            <div style="color: red; font-size: 14px;" class="error-danger">* {{ $message }}</div>
        @enderror
      </div>
      
      <div class="m-t-lg">
        <ul class="list-inline">
          <li>
			<button class="btn btn--form" type="submit" >Đăng nhập</button>            
          </li>
        </ul>
      </div>
	  <div>

	  </div>
	  <div>
			<a href="https://www.youtube.com/" class="forgot_pass ">Bạn đã quên mật khẩu?</a>
	  </div>
    </form>  
  </div>
</div>
</body></html>