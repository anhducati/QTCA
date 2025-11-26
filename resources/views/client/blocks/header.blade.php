<div class="navbar-container">
    <div class="container">
        <!-- ====== start top navbar ====== -->
        <div class="top-navbar style-1">
            <div class="container p-0">
                <div class="row align-items-center">
                    <div class="col-lg-4">
                        <div class="date-weather mb-3 mb-lg-0">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <div class="item">
                                        <div class="icon me-3 pt-1">
                                            <i class="la la-calendar"></i>
                                        </div>
                                        <div class="inf" id="currentDateTime">
                                            <strong>Monday</strong>
                                            <p>Nov 25, 2023</p>
                                        </div>


                                        <script>
                                            // Lấy ngày hiện tại theo múi giờ UTC
                                            var currentDate = new Date();

                                            // Chuyển đổi sang múi giờ GMT+7 (Việt Nam)
                                            var utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
                                            var vnDate = new Date(utc + (3600000 * 7));

                                            // Format ngày thành dạng chuỗi
                                            var options = { year: 'numeric', month: 'long', day: 'numeric' };
                                            var formattedDate = vnDate.toLocaleDateString('vi-VN', options);

                                            // Lấy thứ từ ngày hiện tại
                                            var weekday = vnDate.toLocaleDateString('vi-VN', { weekday: 'long' });

                                            // Hiển thị thứ và ngày đã được format vào thẻ HTML
                                            document.getElementById('currentDateTime').innerHTML = '<strong>' + weekday + '</strong>' + '<p>' + formattedDate + '</p>';
                                        </script>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="item">
                                        <div class="icon me-3 pt-1">
                                            <i class="la la-cloud-sun"></i>
                                        </div>
                                        <div class="inf" >
                                            <strong>32° deg, Cloudy</strong>
                                            <p>Thái Nguyên</p>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <a href="{{ route('client.home') }}" class="logo-brand d-none d-lg-block">
                            <img src="{{ asset('assets/client/img/logo_home1.png') }}" alt="" class="dark-none">
                            <img src="{{ asset('assets/client/img/logo_home1_lt.png') }}" alt="" class="light-none">
                        </a>
                    </div>
                    <div class="col-lg-4">
                        <div class="sub-darkLight">
                            <div class="row text-end align-items-center">
                                <div class="col-6">
                                    <a href="#0"
                                       class="text-uppercase fs-6 border-bottom border-1 border-dark subs">
                                        <i class="la la-envelope fs-5 me-1"></i>
                                        Email
                                    </a>
                                </div>
                                <div class="col-6">
                                    <div class="darkLight-btn">
                                            <span class="icon active" id="light-icon">
                                                <i class="la la-sun"></i>
                                            </span>
                                        <span class="icon" id="dark-icon">
                                                <i class="la la-moon"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="nav-subs-card">
                                <p class="fsz-16px text-uppercase mb-20"> BẢN TIN </p>
                                <div class="sub-form">
                                    <div class="form-group">
                                            <span class="icon">
                                                <i class="la la-envelope"></i>
                                            </span>
                                        <input type="text" class="form-control" placeholder="your email">
                                        <button>GỬI</button>
                                    </div>
                                    
                                </div>
                                <span class="cls"> <i class="la la-times"></i> </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ====== end top navbar ====== -->

        <!-- ====== start navbar ====== -->
        <nav class="navbar navbar-expand-lg navbar-light style-1">
            <div class="container p-0">
                <div class="mob-nav-toggles d-flex align-items-center justify-content-between">
                    <button class="navbarList-icon me-lg-5" data-bs-toggle="offcanvas" href="#offcanvasExample"
                            role="button" aria-controls="offcanvasExample">
                        <span></span>
                        <span></span>
                    </button>
                    <a href="#" class="logo-brand d-block d-lg-none w-50 my-4">
                        <img src="{{ asset('assets/client/img/logo_home1.png') }}" alt="" class="dark-none">
                        <img src="{{ asset('assets/client/img/logo_home1_lt.png') }}" alt="" class="light-none">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('client.home') }}">
                                trang chủ
                            </a>
                        </li>
                        {{-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Bài viết
                            </a>
                            <ul class="dropdownMenu" aria-labelledby="navbarDropdown1">
                                <li><a class="dropdown-item" href="page-blog.html">Blog</a></li>
                                <li><a class="dropdown-item" href="page-author.html">authors</a></li>
                                <li><a class="dropdown-item" href="page-author-details.html">author details</a></li>
                            </ul>
                        </li> --}}

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.blog') }}">
                                Bài viết của bạn
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.contact') }}">
                                Liên hệ
                                
                            </a>
                        </li>

                        

                        



                    </ul>
                    <div class="nav-side">
                        <a href="{{ route('client.login') }}" class="icon-link">
                            <i class="la la-user fs-4"></i>
                        </a>
                        {{-- <a href="#" class="icon-link noti-dot">
                            <i class="la la-shopping-bag fs-4"></i>
                        </a> --}}
                        <a href="#" class="icon-link search-btn-style1">
                            <i class="la la-search fs-4 sOpen-btn"></i>
                            <i class="la la-close fs-4 sClose-btn"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- ====== end navbar ====== -->

        <!-- ====== start nav-search ====== -->
        <div class="nav-search-style1">
            <div class="row justify-content-center align-items-center gx-lg-5">
                <div class="col-lg-4">
                    <div class="info">
                        <h5> Tìm kiếm theo danh mục

                            <br> hoặc tiêu đề tin tức </h5>
                    </div>
                </div>
                <div class="col-lg-6">
                    <form class="form" method="get" action="{{ route('client.blog.search') }}" >
                        <span class="color-777 fst-italic text-capitalize mb-2 fsz-13px">Nhập từ khóa</span>
                        <div class="form-group">
                                <span class="icon">
                                    <i class="la la-search"></i>
                                </span>
                            <input type="text" name="query" class="form-control" placeholder="Từ khóa ... ">
                            <button type="submit">Tìm kiếm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ====== end nav-search ====== -->
    </div>
</div>
