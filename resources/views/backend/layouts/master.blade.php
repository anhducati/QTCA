{{-- resources/views/backend/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Quản lý hệ thống') - Chung Anh</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSS gốc của admin (theo project cũ) --}}
    <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="fixed-sidebar">
    <div id="wrapper">

        {{-- SIDEBAR --}}
        @include('backend.blocks.sidebar')

        <div id="page-wrapper" class="gray-bg">
            {{-- HEADER TOP --}}
            @include('backend.blocks.header')

            {{-- PHẦN TIÊU ĐỀ TRANG + BREADCRUMB + NÚT THÊM (page-header) --}}
            @yield('page-header')

            {{-- NỘI DUNG CHÍNH --}}
            <div class="wrapper wrapper-content animated fadeInRight">
                @yield('content')
            </div>

            {{-- FOOTER (nếu có) --}}
            @includeWhen(View::exists('backend.blocks.footer'), 'backend.blocks.footer')

        </div>
    </div>

    {{-- JS --}}
    <script src="{{ asset('assets/backend/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('assets/backend/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/inspinia.js') }}"></script>

    @stack('scripts')
</body>
</html>
