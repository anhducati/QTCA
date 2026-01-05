@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Dashboard</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Tổng quan CA</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    {{-- Thông báo --}}
    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')
        </div>
    </div>

    {{-- ========================== HÀNG CARD KPI ========================== --}}
    <div class="row">

        {{-- Tổng tồn kho --}}
        <div class="col-lg-3 col-md-6">
            <div class="ibox"
                 onclick="window.location='{{ route('admin.dashboard',['detail'=>'stock']) }}'"
                 style="cursor:pointer;">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">Hiện tại</span>
                    <h5>Tổng tồn kho</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($totalStock) }}</h1>
                    <div class="stat-percent font-bold text-primary">
                        <i class="fa fa-warehouse"></i>
                    </div>
                    <small>Số xe đang trong kho</small>
                </div>
            </div>
        </div>

        {{-- Số lượng bán trong tháng --}}
        <div class="col-lg-3 col-md-6">
            <div class="ibox"
                 onclick="window.location='{{ route('admin.dashboard',['detail'=>'sales_month']) }}'"
                 style="cursor:pointer;">
                <div class="ibox-title">
                    <span class="label label-success pull-right">
                        Tháng {{ $startOfMonth->format('m/Y') }}
                    </span>
                    <h5>Bán trong tháng</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($soldThisMonth) }}</h1>
                    <div class="stat-percent font-bold text-success">
                        <i class="fa fa-motorcycle"></i>
                    </div>
                    <small>Hóa đơn bán lẻ</small>
                </div>
            </div>
        </div>

        {{-- Công nợ NCC --}}
        <div class="col-lg-3 col-md-6">
            <div class="ibox"
                 onclick="window.location='{{ route('admin.dashboard',['detail'=>'supplier_debt']) }}'"
                 style="cursor:pointer;">
                <div class="ibox-title">
                    <span class="label label-warning pull-right">Công nợ NCC</span>
                    <h5>Phiếu nhập</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($unpaidSupplierReceipts) }}</h1>
                    <div class="stat-percent font-bold text-warning">
                        <i class="fa fa-truck-loading"></i>
                    </div>
                    <small>
                        Tổng nợ NCC:
                        <strong>{{ number_format($totalSupplierDebt,0,',','.') }}đ</strong>
                    </small>
                </div>
            </div>
        </div>

        {{-- Công nợ khách hàng --}}
        <div class="col-lg-3 col-md-6">
            <div class="ibox"
                 onclick="window.location='{{ route('admin.dashboard',['detail'=>'customer_debt']) }}'"
                 style="cursor:pointer;">
                <div class="ibox-title">
                    <span class="label label-danger pull-right">Công nợ KH</span>
                    <h5>Khách đang nợ</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{{ number_format($debtorCount) }}</h1>
                    <div class="stat-percent font-bold text-danger">
                        <i class="fa fa-users"></i>
                    </div>

                    <small>Tổng nợ KH:</small><br>
                    <strong class="text-danger">
                        {{ number_format($totalCustomerDebt,0,',','.') }} đ
                    </strong>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================== KHỐI CHI TIẾT KPI – NGAY DƯỚI CARD ========================== --}}
    @if(isset($detailItems) && $detailItems)
    <div class="row" id="detail-kpi">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $detailTitle }}</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-xs btn-default">Đóng</a>
                    </div>
                </div>
                <div class="ibox-content">

                    @if($detail === 'stock')
                        {{-- Chi tiết tồn kho --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Hãng</th>
                                    <th>Dòng xe</th>
                                    <th>Màu</th>
                                    <th>Kho</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Mã xe (Số khung)</th>
                                    <th>Số máy</th>
                                    <th>Năm SX</th>
                                    <th>Giá nhập</th>
                                    <th>Ngày tạo</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($detailItems as $row)
                                    <tr>
                                        <td>{{ $row->brand }}</td>
                                        <td>{{ $row->model }}</td>
                                        <td>{{ $row->color }}</td>
                                        <td>{{ $row->warehouse }}</td>
                                        <td>{{ $row->supplier }}</td>
                                        <td>
                                            {{-- link xem chi tiết xe, hiển thị bằng số khung --}}
                                            <a href="{{ route('admin.vehicles.show', $row->id) }}">
                                                {{ $row->frame_no }}
                                            </a>
                                        </td>
                                        <td>{{ $row->engine_no }}</td>
                                        <td>{{ $row->year }}</td>
                                        <td>{{ number_format($row->purchase_price, 0, ',', '.') }} đ</td>
                                        <td>{{ $row->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    @elseif($detail === 'sales_month')
                        {{-- Chi tiết bán lẻ trong tháng --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Ngày bán</th>
                                    <th>Khách hàng</th>
                                    <th>SĐT</th>
                                    <th>Dòng xe</th>
                                    <th>Số khung</th>
                                    <th>Số máy</th>
                                    <th>Thành tiền</th>
                                    <th>Đã trả</th>
                                    <th>Còn nợ</th>
                                    <th>Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($detailItems as $row)
                                    @php
                                        $status = $row->payment_status;
                                        $statusLabel = $status;
                                        $statusClass = 'default';

                                        if ($status === 'paid') {
                                            $statusLabel = 'Đã thanh toán';
                                            $statusClass = 'success';
                                        } elseif ($status === 'partial') {
                                            $statusLabel = 'Thanh toán một phần';
                                            $statusClass = 'warning';
                                        } elseif ($status === 'unpaid') {
                                            $statusLabel = 'Chưa thanh toán';
                                            $statusClass = 'danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            {{-- link show hóa đơn bằng mã HĐ --}}
                                            <a href="{{ route('admin.vehicle_sales.payments.create', $row->id) }}">
                                                {{ $row->code }}
                                            </a>
                                        </td>
                                        <td>{{ $row->sale_date }}</td>
                                        <td>{{ $row->customer_name }}</td>
                                        <td>{{ $row->customer_phone }}</td>
                                        <td>{{ $row->model }}</td>
                                        <td>{{ $row->frame_no }}</td>
                                        <td>{{ $row->engine_no }}</td>
                                        <td>{{ number_format($row->amount, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($row->paid_amount, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($row->debt_amount, 0, ',', '.') }} đ</td>
                                        <td>
                                            <span class="label label-{{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    @elseif($detail === 'supplier_debt')
                        {{-- Chi tiết phiếu nhập nợ NCC --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Mã phiếu</th>
                                    <th>Ngày nhập</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Số xe chưa thanh toán</th>
                                    <th>Tiền nợ</th>
                                    <th>Tổng tiền phiếu</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($detailItems as $row)
                                    <tr>
                                        <td>
                                            {{-- link show phiếu nhập bằng mã phiếu --}}
                                            <a href="{{ route('admin.import_receipts.show', $row->id) }}">
                                                {{ $row->code }}
                                            </a>
                                        </td>
                                        <td>{{ $row->import_date }}</td>
                                        <td>{{ $row->supplier_name }}</td>
                                        <td>{{ $row->veh_count }}</td>
                                        <td>{{ number_format($row->unpaid_amount, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($row->total_amount, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    @elseif($detail === 'customer_debt')
                        {{-- Chi tiết công nợ khách hàng --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Ngày bán</th>
                                    <th>Khách hàng</th>
                                    <th>SĐT</th>
                                    <th>Dòng xe</th>
                                    <th>Số khung</th>
                                    <th>Số máy</th>
                                    <th>Thành tiền</th>
                                    <th>Đã trả</th>
                                    <th>Còn nợ</th>
                                    <th>Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($detailItems as $row)
                                    @php
                                        $status = $row->payment_status;
                                        $statusLabel = $status;
                                        $statusClass = 'default';

                                        if ($status === 'paid') {
                                            $statusLabel = 'Đã thanh toán';
                                            $statusClass = 'success';
                                        } elseif ($status === 'partial') {
                                            $statusLabel = 'Thanh toán một phần';
                                            $statusClass = 'warning';
                                        } elseif ($status === 'unpaid') {
                                            $statusLabel = 'Chưa thanh toán';
                                            $statusClass = 'danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.vehicle_sales.payments.create', $row->id) }}">
                                                {{ $row->code }}
                                            </a>
                                        </td>
                                        <td>{{ $row->sale_date }}</td>
                                        <td>{{ $row->customer_name }}</td>
                                        <td>{{ $row->customer_phone }}</td>
                                        <td>{{ $row->model }}</td>
                                        <td>{{ $row->frame_no }}</td>
                                        <td>{{ $row->engine_no }}</td>
                                        <td>{{ number_format($row->amount, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($row->paid_amount, 0, ',', '.') }} đ</td>
                                        <td>{{ number_format($row->debt_amount, 0, ',', '.') }} đ</td>
                                        <td>
                                            <span class="label label-{{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="text-right">
                        {{ $detailItems->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================== TOP MODEL & BIỂU ĐỒ THEO DÒNG ========================== --}}
    <div class="row">

        {{-- Rank model --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Top dòng xe bán chạy</h5>
                </div>
                <div class="ibox-content">
                    @if($topModels->count())
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dòng xe</th>
                                    <th>Số lượng</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topModels as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{ $row->total }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Không có dữ liệu bán.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart bán theo model tháng hiện tại --}}
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Số lượng bán theo dòng ({{ $startOfMonth->format('m/Y') }})</h5>
                </div>
                <div class="ibox-content">
                    @if(count($chartModelLabels))
                        <canvas id="chartModel" height="160"></canvas>
                    @else
                        <p class="text-muted">Chưa có dữ liệu bán trong tháng.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ========================== DOANH THU & NHẬP XUẤT ========================== --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title"><h5>Doanh thu 12 tháng</h5></div>
                <div class="ibox-content">
                    <canvas id="chartRevenue" height="160"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title"><h5>Nhập – Xuất 12 tháng</h5></div>
                <div class="ibox-content">
                    <canvas id="chartInOut" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================== TỒN ÍT ========================== --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Cảnh báo dòng xe tồn ít (≤ {{ $lowStockThreshold }} xe)</h5>
                </div>
                <div class="ibox-content">
                    @if($lowStockModels->count())
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dòng xe</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockModels as $i => $m)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $m->name }}</td>
                                    <td>
                                        <span class="badge badge-danger">{{ $m->stock_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Không có dòng xe nào dưới ngưỡng tồn.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ========================== JS BIỂU ĐỒ ========================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ====== Chart doanh thu ======
    new Chart(document.getElementById('chartRevenue'), {
        type: 'line',
        data: {
            labels: @json($chartRevenueLabels),
            datasets: [{
                label: "Doanh thu",
                data: @json($chartRevenueValues),
                borderColor: "#1ab394",
                backgroundColor: "rgba(26,179,148,0.25)",
                borderWidth: 2,
                tension: .3
            }]
        }
    });

    // ====== Chart nhập xuất ======
    new Chart(document.getElementById('chartInOut'), {
        type: 'bar',
        data: {
            labels: @json($chartInOutLabels),
            datasets: [
                {
                    label: "Nhập",
                    data: @json($chartImportValues),
                    backgroundColor: "rgba(54,162,235,0.6)"
                },
                {
                    label: "Xuất/Bán",
                    data: @json($chartExportValues),
                    backgroundColor: "rgba(255,99,132,0.6)"
                }
            ]
        }
    });

    // ====== Chart bán theo model ======
    new Chart(document.getElementById('chartModel'), {
        type: 'bar',
        data: {
            labels: @json($chartModelLabels),
            datasets: [{
                label: "Số lượng",
                data: @json($chartModelValues),
                backgroundColor: "rgba(75,192,192,0.6)"
            }]
        },
        options: { indexAxis: 'y' }
    });
</script>

@endsection
