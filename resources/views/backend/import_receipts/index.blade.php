@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Phiếu nhập kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Danh sách phiếu nhập</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách phiếu nhập kho</h5>
                    <div class="ibox-tools">
                        @canModule('import_receipts', 'create')
                            <a href="{{ route('admin.import_receipts.create') }}"
                               class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Tạo phiếu nhập
                            </a>
                        @endcanModule
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    {{-- BỘ LỌC THEO MÃ PHIẾU + NGÀY --}}
                    <form method="GET" action="{{ route('admin.import_receipts.index') }}" class="m-b-md">
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mã phiếu</label>
                                    <input type="text"
                                           name="code"
                                           value="{{ request('code') }}"
                                           class="form-control"
                                           placeholder="Nhập mã phiếu...">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Từ ngày</label>
                                    <input type="date"
                                           name="from"
                                           value="{{ request('from') }}"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Đến ngày</label>
                                    <input type="date"
                                           name="to"
                                           value="{{ request('to') }}"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" style="margin-top: 22px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-filter"></i> Lọc
                                    </button>
                                    <a href="{{ route('admin.import_receipts.index') }}" class="btn btn-default">
                                        Xóa lọc
                                    </a>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables">
                            <thead>
                            <tr>
                                <th>Mã phiếu</th>
                                <th>Ngày nhập</th>
                                <th>Nhà cung cấp</th>
                                <th>Kho</th>
                                <th>Người lập</th>

                                {{-- Ghi chú thu nhỏ --}}
                                <th style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    Ghi chú
                                </th>

                                <th>Thanh toán</th>
                                <th>Giấy tờ</th>

                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($receipts as $item)
                                @php
                                    $totalVehicles  = $item->vehicles->count();
                                    $paidVehicles   = $item->vehicles->where('supplier_paid', 1)->count();
                                    $docsVehicles   = $item->vehicles->where('registration_received', 1)->count();

                                    // Trạng thái thanh toán
                                    if ($totalVehicles == 0) {
                                        $paymentLabel  = 'Chưa có xe';
                                        $paymentClass  = 'label-default';
                                    } elseif ($paidVehicles == 0) {
                                        $paymentLabel  = 'Chưa thanh toán';
                                        $paymentClass  = 'label-danger';
                                    } elseif ($paidVehicles < $totalVehicles) {
                                        $paymentLabel  = 'Thanh toán một phần ('
                                                        . $paidVehicles . '/' . $totalVehicles . ')';
                                        $paymentClass  = 'label-warning';
                                    } else {
                                        $paymentLabel  = 'Đã thanh toán';
                                        $paymentClass  = 'label-success';
                                    }

                                    // Trạng thái giấy tờ
                                    if ($totalVehicles == 0) {
                                        $docsLabel = 'Chưa có xe';
                                        $docsClass = 'label-default';
                                    } elseif ($docsVehicles == 0) {
                                        $docsLabel = 'Chưa nhận';
                                        $docsClass = 'label-default';
                                    } elseif ($docsVehicles < $totalVehicles) {
                                        $docsLabel = 'Nhận một phần ('
                                                    . $docsVehicles . '/' . $totalVehicles . ')';
                                        $docsClass = 'label-info';
                                    } else {
                                        $docsLabel = 'Đã nhận đủ';
                                        $docsClass = 'label-primary';
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->import_date)->format('d/m/Y') }}</td>
                                    <td>{{ optional($item->supplier)->name }}</td>
                                    <td>{{ optional($item->warehouse)->name }}</td>
                                    <td>{{ optional($item->createdBy)->name }}</td>

                                    <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $item->note }}
                                    </td>

                                    {{-- Thanh toán --}}
                                    <td>
                                        <span class="label {{ $paymentClass }}">
                                            {{ $paymentLabel }}
                                        </span>
                                    </td>

                                    {{-- Giấy tờ --}}
                                    <td>
                                        <span class="label {{ $docsClass }}">
                                            {{ $docsLabel }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('admin.import_receipts.show', $item->id) }}"
                                        class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @canModule('import_receipts', 'update')
                                            <a href="{{ route('admin.import_receipts.edit', $item->id) }}"
                                            class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('import_receipts', 'delete')
                                            <form action="{{ route('admin.import_receipts.destroy', $item->id) }}"
                                                method="POST"
                                                style="display:inline-block"
                                                onsubmit="return confirm('Xóa phiếu nhập này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs" type="submit">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcanModule
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        Chưa có phiếu nhập nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>


                    {{-- Nếu anh dùng paginate() thay vì DataTables server-side thì có thể thêm: --}}
                    {{-- <div class="text-right">{{ $receipts->appends(request()->query())->links() }}</div> --}}

                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('page-scripts')
    <script>
        $(document).ready(function () {
            $('.dataTables').DataTable({
                pageLength: 25,
                responsive: true,
                language: {
                    search: "Tìm:",
                    lengthMenu: "Hiển thị _MENU_ dòng",
                    info: "Hiển thị _START_ đến _END_ của _TOTAL_ phiếu",
                    paginate: {
                        previous: "Trước",
                        next: "Tiếp",
                    }
                }
            });
        });
    </script>
@endsection
