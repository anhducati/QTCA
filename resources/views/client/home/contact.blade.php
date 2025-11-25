@extends('layouts.client')

@section('main')
    <!--Contents-->
    <main>

        

        <!-- ====== end contact image ====== -->

        <!-- ====== start contact form ====== -->
        <section class="tc-contact-form pt-80 pb-80">
            <div class="container">
                <div class="row gx-5">
                    <div class="col-lg-6 border-1 border-end brd-gray">
                        <div class="contact-form-card">
                            <h4 class="fsz-24px text-capitalize mb-10">{{ __('messages.lienhe') }}</h4>

                            <h6 class="my-1">ĐẠI HỌC THỦY LỢI</h6>
                            <p>Số 175 Tây sơn - Quận Đống Đa – Thành phố Hà Nội</p>
                            <p>Điện thoại: (024) 3852 2201</p>
                            <p>Fax: (024) 3563 3351</p>
                            <p>Email: phonghcth@tlu.edu.vn</p>
                            
                            <hr>
                            <h6 class="my-1">PHÒNG ĐÀO TẠO</h6>
                            <p>Tầng 1 - nhà A4</p>
                            <p>Số 175 Tây sơn - Quận Đống Đa – Thành phố Hà Nội</p>
                            <p>Điện thoại: (024) 35631537</p>
                            <p>Email: daotao@tlu.edu.vn</p>

                            <hr>
                            <h6 class="my-1">PHÒNG CHÍNH TRỊ VÀ CÔNG TÁC SINH VIÊN</h6>
                            <p>Tầng 1 - Nhà A1- Trường Đại học Thủy lợi</p>
                            <p>Số 175 Tây sơn - Quận Đống Đa – Thành phố Hà Nội</p>
                            <p>Điện thoại: (024) 35639577</p>
                            <p>Email: p7@tlu.edu.vn</p>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-40 mt-lg-0">
                        <h4 class="fsz-24px text-capitalize mb-30">{{ __('messages.map') }}</h4>
                        <div class="map ">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d476752.97514792474!2d105.46213557193373!3d21.007358814280742!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac8109765ba5%3A0xd84740ece05680ee!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUaOG7p3kgbOG7o2k!5e0!3m2!1svi!2s!4v1718182297333!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ====== end contact form ====== -->

    </main>
    <!--End-Contents-->
@endsection
