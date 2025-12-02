@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Phiếu kiểm kê kho</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Kiểm kê kho</strong></li>
        </ol>
    </div>
    <div class="col-lg-4 text-right m-t-lg">
        @canModule('stock_takes','create')
            <a href="{{ route('admin.stock_takes.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Tạo phiếu kiểm kê
            </a>
        @endcanModule
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            @include('layouts.message')

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Danh sách phiếu kiểm kê</h5>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Mã KK</th>
                                <th>Kho</th>
                                <th>Ngày KK</th>
                                <th>Trạng thái</th>
                                <th>Người tạo</th>
                                <th>Ghi chú</th>
                                <th class="text-center" style="width:120px;">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($stockTakes as $st)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.stock_takes.show', $st->id) }}">
                                            <strong>{{ $st->code }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ optional($st->warehouse)->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($st->stock_take_date)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($st->status === 'draft')
                                            <span class="label label-warning">Nháp</span>
                                        @elseif($st->status === 'confirmed')
                                            <span class="label label-success">Đã xác nhận</span>
                                        @else
                                            <span class="label label-default">{{ $st->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($st->creator)->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($st->note, 50) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.stock_takes.show', $st->id) }}"
                                           class="btn btn-xs btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @if($st->status === 'draft')
                                            @canModule('stock_takes','delete')
                                            <form method="POST"
                                                  action="{{ route('admin.stock_takes.destroy', $st->id) }}"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Xóa phiếu kiểm kê này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcanModule
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Chưa có phiếu kiểm kê nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right">
                        {{ $stockTakes->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
