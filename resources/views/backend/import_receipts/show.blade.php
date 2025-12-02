@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Phiếu nhập kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.import_receipts.index') }}">Danh sách phiếu nhập</a></li>
            <li class="active"><strong>Chi tiết phiếu nhập</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">

        {{-- THÔNG TIN PHIẾU NHẬP --}}
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin phiếu nhập</h5>
                    <div class="ibox-tools">
                        @php
                            // TÍNH NHANH ĐỂ DÙNG CHO NÚT SỬA
                            $vehicles       = $importReceipt->vehicles;
                            $totalVehicles  = $vehicles->count();
                            $totalPurchase  = $vehicles->sum('purchase_price');

                            $hasVehicles     = $totalVehicles > 0;
                            $paidVehicles    = $vehicles->where('supplier_paid', 1)->count();
                            $allPaid         = $hasVehicles && ($paidVehicles == $totalVehicles);
                            $allDocsReceived = $hasVehicles && $vehicles->every(function($v) {
                                return (int)$v->registration_received === 1;
                            });
                        @endphp

                        @canModule('import_receipts', 'update')
                            @if($allDocsReceived)
                                {{-- ĐÃ NHẬN ĐỦ GIẤY TỜ → KHÓA NÚT SỬA --}}
                                <button class="btn btn-default btn-sm" type="button" disabled>
                                    <i class="fa fa-lock"></i> Đã nhận giấy tờ (không sửa)
                                </button>
                            @else
                                <a href="{{ route('admin.import_receipts.edit', $importReceipt->id) }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Sửa
                                </a>
                            @endif
                        @endcanModule
                    </div>
                </div>
                <div class="ibox-content">

                    {{-- KHÔNG CẦN TÍNH LẠI, ĐÃ TÍNH Ở TRÊN --}}
                    <div class="row m-b-sm">
                        <div class="col-md-6">
                            <label>Mã phiếu:</label>
                            <p><strong>{{ $importReceipt->code }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Ngày nhập:</label>
                            <p><strong>{{ \Carbon\Carbon::parse($importReceipt->import_date)->format('d/m/Y') }}</strong></p>
                        </div>
                    </div>

                    <div class="row m-b-sm">
                        <div class="col-md-6">
                            <label>Nhà cung cấp:</label>
                            <p><strong>{{ optional($importReceipt->supplier)->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Kho nhập:</label>
                            <p><strong>{{ optional($importReceipt->warehouse)->name }}</strong></p>
                        </div>
                    </div>

                    <div class="row m-b-sm">
                        <div class="col-md-6">
                            <label>Người lập:</label>
                            <p><strong>{{ optional($importReceipt->createdBy)->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Ngày tạo phiếu:</label>
                            <p>{{ optional($importReceipt->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- TỔNG SỐ XE + TỔNG TIỀN NHẬP --}}
                    <div class="row m-b-sm">
                        <div class="col-md-6">
                            <label>Tổng số xe nhập:</label>
                            <p>
                                <strong>{{ $totalVehicles }}</strong>
                                <span class="text-muted">xe</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label>Tổng tiền nhập:</label>
                            <p>
                                <strong>{{ number_format($totalPurchase, 0, ',', '.') }}</strong>
                                <span class="text-muted">VNĐ</span>
                            </p>
                        </div>
                    </div>

                    <div class="row m-b-sm">
                        <div class="col-md-12">
                            <label>Ghi chú:</label>
                            <p>{{ $importReceipt->note ?: 'Không có' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- NGHIỆP VỤ LIÊN QUAN (XE) --}}
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Nghiệp vụ liên quan</h5>
                </div>
                <div class="ibox-content">

                    <p class="text-muted m-b-sm">
                        Phiếu nhập này dùng để ghi nhận lô xe nhập về kho.  
                        Anh có thể dùng các nút bên dưới để thao tác với xe.
                    </p>

                    {{-- Thêm xe mới vào phiếu --}}
                    <div class="m-b-sm">
                        @if(!$allPaid)
                            <a href="{{ route('admin.import_receipts.vehicles.create', $importReceipt->id) }}"
                               class="btn btn-primary btn-block">
                                <i class="fa fa-plus"></i> Thêm xe mới vào kho
                            </a>
                        @else
                            <button class="btn btn-default btn-block" type="button" disabled>
                                <i class="fa fa-lock"></i> Đã thanh toán NCC - không thể thêm xe
                            </button>
                        @endif
                    </div>

                    {{-- Xem danh sách xe thuộc phiếu này --}}
                    <div class="m-b-sm">
                        <a href="{{ route('admin.vehicles.index', ['import_receipt_id' => $importReceipt->id]) }}"
                           class="btn btn-info btn-block">
                            <i class="fa fa-car"></i> Xem danh sách xe của phiếu này
                        </a>
                    </div>

                    {{-- ĐÃ THANH TOÁN NCC --}}
                    <div class="m-b-sm">
                        @if(!$hasVehicles)
                            <button class="btn btn-default btn-block" disabled>
                                <i class="fa fa-money"></i> Chưa có xe để thanh toán
                            </button>
                        @elseif($allPaid)
                            <button class="btn btn-success btn-block" disabled>
                                <i class="fa fa-check"></i> Đã thanh toán NCC
                            </button>
                        @else
                            <form action="{{ route('admin.import_receipts.mark_paid', $importReceipt->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Xác nhận: Đánh dấu đã thanh toán cho toàn bộ xe trong phiếu này?');">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fa fa-money"></i> ĐÃ THANH TOÁN NCC
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- ĐÃ NHẬN GIẤY TỜ --}}
                    <div class="m-b-sm">
                        @if(!$hasVehicles)
                            <button class="btn btn-default btn-block" disabled>
                                <i class="fa fa-file-text"></i> Chưa có xe để nhận giấy tờ
                            </button>
                        @elseif(!$allPaid)
                            <button class="btn btn-default btn-block" disabled>
                                <i class="fa fa-lock"></i> Chỉ nhận giấy tờ khi ĐÃ THANH TOÁN
                            </button>
                        @elseif($allDocsReceived)
                            <button class="btn btn-success btn-block" disabled>
                                <i class="fa fa-check-square-o"></i> Đã nhận đủ giấy tờ
                            </button>
                        @else
                            <form action="{{ route('admin.import_receipts.mark_docs_received', $importReceipt->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Xác nhận: Đánh dấu ĐÃ NHẬN GIẤY TỜ cho toàn bộ xe trong phiếu này?');">
                                @csrf
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fa fa-file-text"></i> NHẬN GIẤY TỜ
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Danh sách tất cả xe trong kho --}}
                    <div class="m-b-sm">
                        <a href="{{ route('admin.vehicles.index') }}"
                           class="btn btn-default btn-block">
                            <i class="fa fa-list"></i> Danh sách xe trong kho
                        </a>
                    </div>

                    <hr>

                    <p class="text-muted">
                        * Khi đã thanh toán NCC cho toàn bộ xe, phiếu này được khóa,  
                        không thể thêm xe mới để đảm bảo tính chặt chẽ.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
