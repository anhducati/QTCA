<!DOCTYPE html>
<html lang="en">



<head>
    <!-- Metas -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="keywords" content="{{ session()->get('meta-keywords') }}" />
    <meta name="description" content="{{ session()->get('meta-description') }}" />
    <meta name="author" content="{{ session()->get('author') }}" />
    <link rel="icon" href="{{asset('assets/client/img/logo_TLU.ico')}}">

    <!-- Title  -->
    <title>{{ session()->get('title-page') }}</title>
    

    @include('client.blocks.style')

    @yield('style')


</head>

<body class="home-style1">

<!-- ====== start loading page ====== -->
{{--<div class="loading-page style1">--}}
{{--    <div class="lad-cont">--}}
{{--        <h2 class="loading loading01">--}}
{{--            <span>.</span>--}}
{{--            <span>n</span>--}}
{{--            <span>e</span>--}}
{{--            <span>w</span>--}}
{{--            <span>z</span>--}}
{{--            <span>i</span>--}}
{{--            <span>n</span>--}}
{{--        </h2>--}}
{{--        <small class="loading loading01">--}}
{{--            <span>l</span>--}}
{{--            <span>o</span>--}}
{{--            <span>a</span>--}}
{{--            <span>d</span>--}}
{{--            <span>i</span>--}}
{{--            <span>n</span>--}}
{{--            <span>g</span>--}}
{{--        </small>--}}
{{--    </div>--}}
{{--</div>--}}
<!-- ====== end loading page ====== -->

<!-- ====== start navbar-container ====== -->
@include('client.blocks.header2')

<!-- ====== start navbar-container ====== -->

<!--Contents-->

@yield('main')

<!--End-Contents-->

<!-- ====== start footer ====== -->
{{-- chatbot --}}
{{-- <iframe
    src="https://www.chatbase.co/chatbot-iframe/yFTNH3Pm4Nfpv5WN8HP4D"
    width="100%"
    style="height: 100%; min-height: 500px"
    frameborder="0"
></iframe> --}}
<script>
    window.embeddedChatbotConfig = {
    chatbotId: "yFTNH3Pm4Nfpv5WN8HP4D",
    domain: "www.chatbase.co"
    }
    </script>
    <script
    src="https://www.chatbase.co/embed.min.js"
    chatbotId="yFTNH3Pm4Nfpv5WN8HP4D"
    domain="www.chatbase.co"
    defer>
    </script>
@include('client.blocks.footer')

<!-- ====== end footer ====== -->

<!-- ====== start to top button ====== -->
<!-- <div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 220.587;"></path></svg>
</div> -->
<!-- ====== end to top button ====== -->

<!-- ====== request ====== -->
@include('client.blocks.scripts')


</body>


<!-- Mirrored from newzin-html.themescamp.com/home-default.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 29 Feb 2024 18:43:35 GMT -->
</html>
