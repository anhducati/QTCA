@extends('layouts.client')


@section('main')

    <div class="tc-404-page">
        <main>

            <!-- ====== start about-team ====== -->
            <section class="tc-404-info text-center">
                <div class="container">
                    <h1> 404 </h1>
                    <h3> Không ổn! Không tìm thấy trang. </h3>
                    <p class="color-777"> Xin lỗi, không thể tìm thấy trang được yêu cầu. <a href="#" class="fw-bold color-000">  Hãy thử tìm kiếm? </a> </p>
                    <a href="{{ route('client.home') }}" class="butn bg-main text-white hover-shadow mt-50">
                        <span> Trở lại trang chủ </span>
                    </a>
                </div>
            </section>
            <!-- ====== end about-team ====== -->


            <!-- ====== start modals ====== -->

            <div class="offcanvas offcanvas-start sidebar-popup-style1" tabindex="-1" id="offcanvasExample"
                 aria-labelledby="offcanvasExampleLabel">
                <div class="offcanvas-header">
                    <div class="logo">
                        <img src="assets/img/logo_home1.png" alt="" class="dark-none">
                        <img src="assets/img/logo_home1_lt.png" alt="" class="light-none">
                    </div>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mt-4">
                    <h6 class="color-000 text-uppercase mb-10 ltspc-1"> about us <i class="la la-angle-right ms-1"></i> </h6>
                    <div class="text">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem optio tempora quia iure quae. Soluta corporis quidem aperiam amet nihil.
                    </div>

                    <div class="sidebar-categories mt-40">
                        <h6 class="color-000 text-uppercase mb-30 ltspc-1"> categories <i class="la la-angle-right ms-1"></i> </h6>
                        <a href="#" class="cat-card">
                            <div class="img img-cover">
                                <img src="assets/img/bussines/1.png" alt="">
                            </div>
                            <div class="info">
                                <h5>bussines</h5>
                                <span class="num">12</span>
                            </div>
                        </a>
                        <a href="#" class="cat-card">
                            <div class="img img-cover">
                                <img src="assets/img/trend/3.png" alt="">
                            </div>
                            <div class="info">
                                <h5>technology</h5>
                                <span class="num">14</span>
                            </div>
                        </a>
                        <a href="#" class="cat-card">
                            <div class="img img-cover">
                                <img src="assets/img/must_read/3.png" alt="">
                            </div>
                            <div class="info">
                                <h5>culture</h5>
                                <span class="num">20</span>
                            </div>
                        </a>
                        <a href="#" class="cat-card">
                            <div class="img img-cover">
                                <img src="assets/img/videos/1.png" alt="">
                            </div>
                            <div class="info">
                                <h5>videos</h5>
                                <span class="num">14</span>
                            </div>
                        </a>
                    </div>
                    <div class="sidebar-contact-info mt-50">
                        <h6 class="color-000 text-uppercase mb-20 ltspc-1"> Contact & follow <i class="la la-angle-right ms-1"></i> </h6>
                        <ul class="m-0">
                            <li class="mb-3">
                                <i class="las la-map-marker me-2 color-main fs-5"></i>
                                <a href="#">streat name 12, hollywood City, USA</a>
                            </li>
                            <li class="mb-3">
                                <i class="las la-envelope me-2 color-main fs-5"></i>
                                <a href="#">Newzin@gmail.com</a>
                            </li>
                            <li class="mb-3">
                                <i class="las la-phone-volume me-2 color-main fs-5"></i>
                                <a href="#">+12 123 456 789</a>
                            </li>
                        </ul>
                        <div class="social-links">
                            <a href="#">
                                <i class="la la-twitter"></i>
                            </a>
                            <a href="#">
                                <i class="la la-facebook-f"></i>
                            </a>
                            <a href="#">
                                <i class="la la-instagram"></i>
                            </a>
                            <a href="#">
                                <i class="la la-youtube"></i>
                            </a>
                            <a href="#">
                                <i class="la la-spotify"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ====== end modals ====== -->

        </main>

    </div>
@endsection
