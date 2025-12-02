@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        @if(!empty($importReceipt))
            <h2>Danh sách xe của phiếu nhập {{ $importReceipt->code }}</h2>
        @else
            <h2>Danh sách xe trong kho</h2>
        @endif

        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">
                <strong>Danh sách xe</strong>
            </li>
        </ol>
    </div>

    <div class="col-lg-4 text-right m-t-lg">
        @if(!empty($importReceipt))
            <a href="{{ route('admin.import_receipts.show', $importReceipt->id) }}"
               class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Quay lại phiếu nhập {{ $importReceipt->code }}
            </a>
        @endif

        @canModule('vehicles', 'create')
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Thêm xe mới
            </a>
        @endcanModule
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox">
                <div class="ibox-title">
                    @if(!empty($importReceipt))
                        <h5>
                            Xe thuộc phiếu nhập {{ $importReceipt->code }}
                            @if($importReceipt->import_date)
                                <small>- Ngày nhập:
                                    {{ \Carbon\Carbon::parse($importReceipt->import_date)->format('d/m/Y') }}
                                </small>
                            @endif
                        </h5>
                    @else
                        <h5>Danh sách tất cả xe trong kho</h5>
                    @endif
                </div>

                <div class="ibox-content">

                    @include('layouts.message')

                    @if(!empty($importReceipt))
                        <div class="alert alert-info">
                            <p>
                                <strong>Phiếu nhập:</strong> {{ $importReceipt->code }} <br>
                                <strong>Nhà cung cấp:</strong> {{ optional($importReceipt->supplier)->name ?? 'N/A' }} <br>
                                <strong>Kho nhập:</strong> {{ optional($importReceipt->warehouse)->name ?? 'N/A' }}
                            </p>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Hãng</th>
                                <th>Dòng xe</th>
                                <th>Màu</th>
                                <th>Số khung</th>
                                <th>Số máy</th>
                                <th>Năm</th>
                                <th>Kho</th>
                                <th>Giá nhập (VNĐ)</th>
                                <th>Trạng thái</th>
                                <th>Biển số</th>
                                <th>Phiếu nhập</th>
                                <th>Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($vehicles as $index => $vehicle)
                                @php
                                    $st = $vehicle->status;
                                    $isInStock = ($st == 0 || $st == '0' || $st == 'in_stock');
                                    $isSold    = ($st == 1 || $st == '1' || $st == 'sold');
                                    $isDeposit = ($st == 2 || $st == '2');
                                    $isDemo    = ($st == 'demo');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ optional($vehicle->brand)->name ?? optional(optional($vehicle->model)->brand)->name }}</td>
                                    <td>{{ optional($vehicle->model)->name }}</td>
                                    <td>{{ optional($vehicle->color)->name }}</td>
                                    <td>{{ $vehicle->frame_no }}</td>
                                    <td>{{ $vehicle->engine_no }}</td>
                                    <td>{{ $vehicle->year }}</td>
                                    <td>{{ optional($vehicle->warehouse)->name }}</td>
                                    <td class="text-right">
                                        @if($vehicle->purchase_price)
                                            {{ number_format($vehicle->purchase_price, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($isInStock)
                                            <span class="label label-primary">Trong kho</span>
                                        @elseif($isDemo)
                                            <span class="label label-warning">DEMO</span>
                                        @elseif($isDeposit)
                                            <span class="label label-info">Đặt cọc</span>
                                        @elseif($isSold)
                                            <span class="label label-success">Đã bán</span>
                                        @else
                                            <span class="label label-default">{{ $st }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $vehicle->license_plate }}</td>
                                    <td>
                                        @if($vehicle->importReceipt)
                                            <a href="{{ route('admin.import_receipts.show', $vehicle->importReceipt->id) }}">
                                                {{ $vehicle->importReceipt->code }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Xem chi tiết --}}
                                        @canModule('vehicles', 'read')
                                            <a href="{{ route('admin.vehicles.show', $vehicle->id) }}"
                                               class="btn btn-xs btn-info m-b-xs">
                                                <i class="fa fa-eye"></i> Xem
                                            </a>
                                        @endcanModule

                                        {{-- Sửa --}}
                                        @canModule('vehicles', 'update')
                                            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}"
                                               class="btn btn-xs btn-primary m-b-xs">
                                                <i class="fa fa-pencil"></i> Sửa
                                            </a>

                                            {{-- Kết thúc demo (nếu đang demo) --}}
                                            @if($isDemo)
                                                <form action="{{ route('admin.vehicles.end_demo', $vehicle->id) }}"
                                                      method="POST"
                                                      style="display:inline-block"
                                                      onsubmit="return confirm('Kết thúc DEMO và cho xe về kho?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-warning m-b-xs">
                                                        <i class="fa fa-refresh"></i> Kết thúc demo
                                                    </button>
                                                </form>
                                            @endif
                                        @endcanModule

                                        {{-- Xóa --}}
                                        @canModule('vehicles', 'delete')
                                            <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}"
                                                  method="POST"
                                                  style="display:inline-block"
                                                  onsubmit="return confirm('Xóa xe này khỏi hệ thống?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger m-b-xs">
                                                    <i class="fa fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        @endcanModule
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted">
                                        Không có xe nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>

                            @if($vehicles->count() > 0)
                                <tfoot>
                                <tr>
                                    <th colspan="7" class="text-right">
                                        Tổng:
                                    </th>
                                    <th class="text-right">
                                        {{ $totalVehicles }} xe
                                    </th>
                                    <th class="text-right">
                                        {{ number_format($totalPurchase, 0, ',', '.') }} VNĐ
                                    </th>
                                    <th colspan="4"></th>
                                </tr>
                                </tfoot>
                            @endif

                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection


@section('page-scripts')
<script>
$(function(){
    $('.dataTables').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        language: {
            search: "Tìm kiếm: ",
            lengthMenu: "Hiển thị _MENU_ mục",
            info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
            paginate: { previous: "Trước", next: "Tiếp" }
        },
        buttons: [
            { extend: 'copy'},
            { extend: 'csv'},
            { extend: 'excel', title: 'DanhSachXe'},
            { extend: 'pdf', title: 'DanhSachXe'},
            { extend: 'print',
              customize: function(win){
                  $(win.document.body).addClass('white-bg')
                      .css('font-size', '10px');
                  $(win.document.body).find('table')
                      .addClass('compact')
                      .css('font-size', 'inherit');
              }
            }
        ]
    });
});
</script>
@endsection
