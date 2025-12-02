<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản trị</title>

    <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <!-- Morris -->
    <link href="{{ asset('assets/backend/css/plugins/morris/morris-0.4.3.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/backend/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">

    <!-- Sweet alert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    @yield('page-css')

    <!-- SlimSelect -->
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.css" rel="stylesheet" />

    <!-- switchery -->
    <script src="{{ asset('assets/backend/js/plugins/switchery/switchery.js') }}"></script>
    <link href="{{ asset('assets/backend/css/plugins/switchery/switchery.css') }}" rel="stylesheet">

    <!-- jquery -->
    <script src="{{ asset('assets/backend/js/jquery-3.1.1.min.js') }}"></script>

</head>

<body>
<div id="wrapper">

    {{-- Sidebar --}}
    @include('backend.blocks.sidebar')

    <div id="page-wrapper" class="gray-bg">

        {{-- Header --}}
        @include('backend.blocks.header')

        @yield('main')

        {{-- Footer --}}
        @include('backend.blocks.footer')

    </div>

</div>

@include('backend.blocks.scripts')

@yield('page-scripts')



<!-- =====================================================
     AUTO FORMAT TIỀN VNĐ DÙNG CHUNG TOÀN HỆ THỐNG
====================================================== -->
<script>
    function CA_FormatMoney_RegisterEvents(root = document) {
        root.querySelectorAll('input.money-input').forEach(function (el) {

            function formatMoney() {
                let raw = el.value.replace(/\D/g, '');
                if (!raw) {
                    el.value = '';
                    return;
                }
                el.value = Number(raw).toLocaleString('vi-VN');
            }

            if (el.value) {
                formatMoney();
            }

            el.addEventListener('input', function () {
                formatMoney();
                el.selectionStart = el.selectionEnd = el.value.length;
            });

            el.addEventListener('paste', function () {
                setTimeout(formatMoney, 0);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        CA_FormatMoney_RegisterEvents();
    });

    // Dùng khi clone template
    function CA_FormatMoney_ApplyTo(element) {
        CA_FormatMoney_RegisterEvents(element);
    }
</script>



<!-- =====================================================
     SLIMSELECT TỰ ĐỘNG, CHỈ CẦN class="slim-select"
====================================================== -->
<script src="https://cdn.jsdelivr.net/npm/slim-select@2.7.1/dist/slimselect.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function initSlimSelect(root = document) {
        root.querySelectorAll('select.slim-select').forEach(function (el) {
            new SlimSelect({
                select: el,
                settings: {
                    searchPlaceholder: 'Tìm kiếm...',
                    searchHighlight: true,
                }
            });
        });
    }

    // chạy cho toàn trang
    initSlimSelect();

    // Hàm cho phép view gọi cho DOM động (clone template)
    window.CA_InitSlimSelect = function (element) {
        initSlimSelect(element);
    };

});
</script>



<!-- =====================================================
     SCRIPT CŨ (chart demo...) – giữ nguyên
====================================================== -->
<script>
    $(document).ready(function() {
        var d1 = [[1262304000000, 6], [1264982400000, 3057], [1267401600000, 20434]];
        var d2 = [[1262304000000, 5], [1264982400000, 200], [1267401600000, 1605]];

        var data1 = [
            { label: "Data 1", data: d1, color: '#17a084'},
            { label: "Data 2", data: d2, color: '#127e68' }
        ];

        $.plot($("#flot-chart1"), data1, { xaxis:{ tickDecimals:0 }, series:{ lines:{ show:true, fill:true }}});

        var lineData = {
            labels: ["January", "February", "March", "April"],
            datasets: [
                {
                    label: "Example dataset",
                    backgroundColor: "rgba(26,179,148,0.5)",
                    borderColor: "rgba(26,179,148,0.7)",
                    data: [48, 48, 60, 39]
                },
                {
                    label: "Example dataset",
                    backgroundColor: "rgba(220,220,220,0.5)",
                    borderColor: "rgba(220,220,220,1)",
                    data: [65, 59, 40, 51]
                }
            ]
        };

        var ctx = document.getElementById("lineChart").getContext("2d");
        new Chart(ctx, {type: 'line', data: lineData});
    });
</script>

</body>
</html>
