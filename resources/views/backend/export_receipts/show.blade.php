@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Phiếu xuất kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.export_receipts.index') }}">Danh sách phiếu xuất</a></li>
            <li class="active"><strong>Chi tiết phiếu xuất</strong></li>
        </ol>
    </div>
    <div class="col-lg-2 text-right m-t-lg">
        <a href="{{ route('admin.export_receipts.index') }}" class="btn btn-default">
            <i class="fa fa-list"></i> Danh sách phiếu xuất
        </a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')
        </div>
    </div>

    <div class="row">

        {{-- THÔNG TIN PHIẾU XUẤT --}}
        <div class="col-lg-5">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin phiếu xuất</h5>
                    <div class="ibox-tools">
                        @canModule('export_receipts', 'update')
                            <a href="{{ route('admin.export_receipts.edit', $exportReceipt->id) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i> Sửa phiếu
                            </a>
                        @endcanModule

                        @canModule('export_receipts', 'delete')
                            <form action="{{ route('admin.export_receipts.destroy', $exportReceipt->id) }}"
                                  method="POST"
                                  style="display:inline-block"
                                  onsubmit="return confirm('Xóa phiếu xuất này? Toàn bộ xe gắn với phiếu có thể bị ảnh hưởng!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Xóa
                                </button>
                            </form>
                        @endcanModule
                    </div>
                </div>
                <div class="ibox-content">

                    @php
                        $type          = $exportReceipt->export_type;
                        $status        = $exportReceipt->payment_status;
                        // Đã thu tiền: paid hoặc paid_docs
                        $isPaid        = in_array($status, ['paid', 'paid_docs']);
                        // Đã giao giấy tờ: dùng chính payment_status
                        $docsDelivered = ($status === 'paid_docs');
                    @endphp

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Mã phiếu:</label>
                            <p><strong>{{ $exportReceipt->code }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Ngày xuất:</label>
                            <p><strong>{{ optional($exportReceipt->export_date)->format('d/m/Y') }}</strong></p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Kho xuất:</label>
                            <p><strong>{{ optional($exportReceipt->warehouse)->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Loại xuất kho:</label>
                            <p>
                                @if($type === 'sell')
                                    <span class="label label-primary">Bán buôn</span>
                                @elseif($type === 'transfer')
                                    <span class="label label-info">Chuyển kho</span>
                                @elseif($type === 'demo')
                                    <span class="label label-warning">Xe demo / sự kiện</span>
                                @else
                                    <span class="label label-default">{{ $type }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Đối tác nhận xe (nhà cung cấp) --}}
                    <div class="row m-b-xs">
                        <div class="col-md-12">
                            <label>Đối tác nhận xe (Nhà cung cấp):</label>
                            <p>
                                @if($exportReceipt->export_type === 'sell')
                                    <strong>{{ optional($exportReceipt->supplier)->name ?? 'Không xác định' }}</strong>
                                @else
                                    <span class="text-muted">Không áp dụng cho loại xuất này.</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Người lập phiếu:</label>
                            <p><strong>{{ optional($exportReceipt->createdBy)->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label>Người duyệt:</label>
                            <p>{{ optional($exportReceipt->approvedBy)->name ?? 'Chưa duyệt' }}</p>
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Ngày tạo phiếu:</label>
                            <p>{{ optional($exportReceipt->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>Hạn thanh toán:</label>
                            <p>{{ optional($exportReceipt->due_date)->format('d/m/Y') ?: 'Không đặt hạn' }}</p>
                        </div>
                    </div>

                    {{-- TỔNG SỐ XE + TỔNG TIỀN --}}
                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Tổng số xe xuất:</label>
                            <p>
                                <strong>{{ $totalVehicles }}</strong>
                                <span class="text-muted">xe</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label>Tổng tiền xuất:</label>
                            <p>
                                <strong>{{ number_format($totalAmount, 0, ',', '.') }}</strong>
                                <span class="text-muted">VNĐ</span>
                            </p>
                        </div>
                    </div>

                    {{-- TRẠNG THÁI THANH TOÁN --}}
                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Thanh toán:</label>
                            <p>
                                @if($status === 'paid_docs')
                                    <span class="label label-success">Đã thanh toán & giao giấy tờ</span>
                                @elseif($status === 'paid')
                                    <span class="label label-success">Đã thanh toán</span>
                                @elseif($status === 'partial')
                                    <span class="label label-warning">Thanh toán một phần</span>
                                @elseif($status === 'unpaid')
                                    <span class="label label-danger">Chưa thanh toán</span>
                                @else
                                    <span class="label label-default">{{ $status ?? 'Không rõ' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label>Tiền đã thu / Còn nợ:</label>
                            <p>
                                Đã thu:
                                <strong>{{ number_format($exportReceipt->paid_amount ?? 0, 0, ',', '.') }}</strong> VNĐ<br>
                                Còn nợ:
                                <strong>{{ number_format($exportReceipt->debt_amount ?? 0, 0, ',', '.') }}</strong> VNĐ
                            </p>
                        </div>
                    </div>

                    {{-- NGHIỆP VỤ: NHẬN TIỀN & GIAO GIẤY TỜ --}}
                    <div class="row m-b-xs">
                        <div class="col-md-6">
                            <label>Nghiệp vụ nhận tiền:</label>
                            @canModule('export_receipts', 'update')
                                @if($isPaid)
                                    <p>
                                        <button class="btn btn-success btn-block" type="button" disabled>
                                            <i class="fa fa-check"></i> Đã nhận đủ tiền
                                        </button>
                                    </p>
                                @else
                                    <form action="{{ route('admin.export_receipts.mark_paid', $exportReceipt->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Xác nhận: Đánh dấu phiếu này đã nhận đủ tiền?');">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-block">
                                            <i class="fa fa-money"></i> NHẬN TIỀN
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p><span class="text-muted">Bạn không có quyền cập nhật thanh toán.</span></p>
                            @endcanModule
                        </div>

                        <div class="col-md-6">
                            <label>Nghiệp vụ giao giấy tờ:</label>
                            @canModule('export_receipts', 'update')
                                @if(!$isPaid)
                                    <p>
                                        <button class="btn btn-default btn-block" type="button" disabled>
                                            <i class="fa fa-lock"></i> GIAO GIẤY TỜ (chỉ khi đã nhận tiền)
                                        </button>
                                    </p>
                                @elseif($docsDelivered)
                                    <p>
                                        <button class="btn btn-success btn-block" type="button" disabled>
                                            <i class="fa fa-check-square-o"></i> Đã giao giấy tờ
                                        </button>
                                    </p>
                                @else
                                    <form action="{{ route('admin.export_receipts.mark_docs_delivered', $exportReceipt->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Xác nhận: Đánh dấu ĐÃ GIAO ĐỦ GIẤY TỜ cho khách?');">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fa fa-file-text"></i> GIAO GIẤY TỜ
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p><span class="text-muted">Bạn không có quyền cập nhật giấy tờ.</span></p>
                            @endcanModule
                        </div>
                    </div>

                    <div class="row m-b-xs">
                        <div class="col-md-12">
                            <label>Ghi chú:</label>
                            <p>{{ $exportReceipt->note ?: 'Không có' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- DANH SÁCH XE ĐÃ XUẤT --}}
        <div class="col-lg-7">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách xe xuất kho</h5>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Số khung</th>
                                <th>Dòng xe</th>
                                <th>Màu</th>
                                <th>Kho hiện tại</th>
                                <th>Giá bán</th>
                                <th>Ghi chú</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($exportReceipt->items as $idx => $item)
                                @php
                                    $vehicle = $item->vehicle;
                                    $model   = $vehicle->model ?? $item->model ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ optional($vehicle)->frame_no }}</td>
                                    <td>{{ optional($model)->name }}</td>
                                    <td>{{ optional($vehicle->color)->name }}</td>
                                    <td>{{ optional($vehicle->warehouse)->name }}</td>
                                    <td class="text-right">
                                        {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $item->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Chưa có xe nào trong phiếu xuất này.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                            @if($exportReceipt->items->count() > 0)
                                <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Tổng cộng:</th>
                                    <th class="text-right">
                                        {{ number_format($totalAmount, 0, ',', '.') }}
                                    </th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <hr>

                    <div class="text-right">
                        <a href="{{ route('admin.export_receipts.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

@endsection
