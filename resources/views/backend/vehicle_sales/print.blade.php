<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">

    <style>
        /* KHAI BÁO KHỔ A4 VÀ LỀ */
        @page {
            size: A4;
            margin: 10mm 15mm 12mm 15mm; /* trên, phải, dưới, trái */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            line-height: 1.35;
            color: #000;
        }

        .contract-wrap {
            width: 100%;
        }

        .txt-red { color: #c00; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { margin-bottom: 4px; }
        .label {
            width: 165px;
            display: inline-block;
        }
        .mt-5 { margin-top: 5px; }
        .mt-10 { margin-top: 10px; }
        .mt-15 { margin-top: 15px; }
    </style>
</head>

<body>
<div class="contract-wrap">

    {{-- TIÊU ĐỀ QUỐC HIỆU --}}
    <div class="center txt-red bold" style="font-size:16px;">
        CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM<br>
        Độc lập - Tự do - Hạnh phúc
    </div>

    {{-- TÊN CÔNG TY --}}
    <div class="center txt-red bold mt-10" style="font-size:20px;">
        Công Ty Thương Mại Chung Anh
    </div>

    <div class="center txt-red mt-5" style="font-size:12px;">
        BÁN VÀ BẢO HÀNH XE MÁY CHÍNH HIỆU: HONDA – YAMAHA <br>
        Đổi xe cũ lấy xe mới – Cung cấp phụ tùng xe máy chính hiệu <br>
        ĐC: Thôn Yên Khoái – Nga Yên – Nga Sơn – Thanh Hóa ·
        ĐT: 02373.959.888 · Zalo: 082.338.4628
    </div>

    <div class="center txt-red bold mt-15" style="font-size:18px;">
        HỢP ĐỒNG MUA BÁN XE MÁY
    </div>

    {{-- ==================== BÊN A ====================== --}}
    <div class="mt-10 line"><span class="label bold">Bên A (Bên bán):</span> Công ty TNHH TM Chung Anh</div>
    <div class="line"><span class="label bold">Địa chỉ:</span> Thôn Yên Khoái – Nga Yên – Nga Sơn – Thanh Hóa</div>
    <div class="line"><span class="label bold">Mã số thuế:</span> 2801174805</div>

    {{-- ==================== BÊN B ====================== --}}
    <div class="mt-10 line"><span class="label bold">Bên B (Bên mua):</span> {{ $sale->customer->name }}</div>
    <div class="line"><span class="label bold">Địa chỉ:</span> {{ $sale->customer->address }}</div>
    <div class="line"><span class="label bold">Số điện thoại:</span> {{ $sale->customer->phone }}</div>

    {{-- ==================== THÔNG TIN XE ====================== --}}
    <div class="mt-10 line">
        <span class="label bold">Loại xe:</span>
        {{ optional($sale->vehicle->model)->name }}
    </div>
    <div class="line">
        <span class="label bold">Màu sơn:</span>
        {{ optional($sale->vehicle->color)->name }}
    </div>
    <div class="line"><span class="label bold">Số khung:</span> {{ $sale->vehicle->frame_no }}</div>
    <div class="line"><span class="label bold">Số máy:</span>  {{ $sale->vehicle->engine_no }}</div>

    {{-- ==================== TIỀN ====================== --}}
    @php
        $price = number_format($sale->sale_price,0,',','.');
        $paid  = number_format($sale->paid_amount,0,',','.');
        $debt  = number_format($sale->debt_amount,0,',','.');
        $saleDate = \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y');
    @endphp

    <div class="mt-10 line"><span class="label bold">Giá bán:</span> {{ $price }} VNĐ</div>
    <div class="line"><span class="label bold">Đã thanh toán:</span> {{ $paid }} VNĐ</div>
    <div class="line"><span class="label bold">Còn nợ:</span> <strong>{{ $debt }} VNĐ</strong></div>

    @if($sale->debt_amount > 0)
        <div class="line">
            <span class="label bold">Hẹn trả:</span>
            {{ $saleDate }}
        </div>
    @endif

    {{-- ==================== GHI CHÚ ====================== --}}
    @if($sale->note)
        <div class="mt-10 line">
            <span class="label bold">Ghi chú:</span> {!! nl2br(e($sale->note)) !!}
        </div>
    @endif

    {{-- ==================== CHỮ KÝ ====================== --}}
    <div style="margin-top:35px;">
        <table width="100%">
            <tr>
                <td class="center bold">Người mua xe</td>
                <td class="center bold">Người bán (Trần Thị Thu Hằng)</td>
            </tr>
            <tr>
                <td class="center" style="height:90px;"></td>
                <td class="center"></td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
