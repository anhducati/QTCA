@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Phiếu kiểm kê {{ $stockTake->code }}</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.stock_takes.index') }}">Kiểm kê kho</a></li>
            <li class="active"><strong>{{ $stockTake->code }}</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        <a href="{{ route('admin.stock_takes.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>

        @if($stockTake->status === 'draft')
            @canModule('stock_takes','update')
                <form method="POST"
                      action="{{ route('admin.stock_takes.confirm', $stockTake->id) }}"
                      style="display:inline-block;"
                      onsubmit="return confirm('Xác nhận kiểm kê và tạo phiếu điều chỉnh kho?');">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check"></i> Xác nhận & tạo điều chỉnh kho
                    </button>
                </form>
            @endcanModule
        @endif
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')
        </div>
    </div>

    <div class="row">

        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin phiếu kiểm kê</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-borderless m-b-none">
                        <tr>
                            <th>Mã KK</th>
                            <td><strong>{{ $stockTake->code }}</strong></td>
                        </tr>
                        <tr>
                            <th>Kho</th>
                            <td>{{ optional($stockTake->warehouse)->name }}</td>
                        </tr>
                        <tr>
                            <th>Ngày KK</th>
                            <td>{{ \Carbon\Carbon::parse($stockTake->stock_take_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($stockTake->status === 'draft')
                                    <span class="label label-warning">Nháp</span>
                                @elseif($stockTake->status === 'confirmed')
                                    <span class="label label-success">Đã xác nhận</span>
                                @else
                                    <span class="label label-default">{{ $stockTake->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Người tạo</th>
                            <td>{{ optional($stockTake->creator)->name }}</td>
                        </tr>
                        <tr>
                            <th>Ghi chú</th>
                            <td>{{ $stockTake->note }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Nếu anh muốn thêm khu "xe lạ" riêng cũng được --}}
        </div>

        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách xe kiểm kê</h5>
                </div>
                <div class="ibox-content">

                    @if($stockTake->status === 'draft')
                    <form method="POST" action="{{ route('admin.stock_takes.update_items', $stockTake->id) }}">
                        @csrf
                    @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dòng xe</th>
                                    <th>Số khung</th>
                                    <th>Số máy</th>
                                    <th>Trong hệ thống</th>
                                    <th>Có mặt</th>
                                    <th>Ghi chú</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($stockTake->items as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            {{ optional(optional($item->vehicle)->model)->name }}
                                        </td>
                                        <td>{{ $item->frame_no }}</td>
                                        <td>{{ $item->engine_no }}</td>
                                        <td>
                                            @if($item->system_exists)
                                                <span class="label label-primary">Có</span>
                                            @else
                                                <span class="label label-danger">Xe lạ</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($stockTake->status === 'draft')
                                                <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                                                <input type="checkbox" name="items[{{ $idx }}][is_present]"
                                                       value="1" {{ $item->is_present ? 'checked' : '' }}>
                                            @else
                                                @if($item->is_present)
                                                    <span class="label label-success">Có</span>
                                                @else
                                                    <span class="label label-warning">Mất</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($stockTake->status === 'draft')
                                                <input type="text" class="form-control"
                                                       name="items[{{ $idx }}][note]"
                                                       value="{{ $item->note }}">
                                            @else
                                                {{ $item->note }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($stockTake->status === 'draft')
                            <div class="m-t-sm">
                                <h5>Thêm xe lạ (không có trong hệ thống)</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Số khung</th>
                                            <th>Số máy</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($i=0; $i<3; $i++)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="new_items[{{ $i }}][frame_no]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="new_items[{{ $i }}][engine_no]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                           name="new_items[{{ $i }}][note]">
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Lưu danh sách kiểm kê
                                </button>
                            </div>
                        @endif

                    @if($stockTake->status === 'draft')
                    </form>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
