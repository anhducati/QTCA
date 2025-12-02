@extends('layouts.panel')

@section('main')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Màu xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"><strong>Danh sách màu xe</strong></li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">

            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Danh sách màu xe</h5>
                    <div class="ibox-tools">
                        @canModule('colors','create')
                            <a href="{{ route('admin.colors.create') }}" class="btn btn-danger btn-xs">
                                <i class="fa fa-plus"></i> Thêm màu xe
                            </a>
                        @endcanModule
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>

                <div class="ibox-content">
                    @include('layouts.message')

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Mã màu</th>
                                <th>Tên màu</th>
                                <th>Xem màu</th>
                                <th>Ghi chú</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- $colors từ ColorController@index --}}
                            @forelse($colors as $index => $color)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $color->code }}</td>
                                    <td>{{ $color->name }}</td>
                                    <td>
                                        @if($color->hex_code)
                                            <span class="label" style="background: {{ $color->hex_code }};">
                                                &nbsp;&nbsp;&nbsp;
                                            </span>
                                            <span class="m-l-xs text-muted">{{ $color->hex_code }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $color->note }}</td>
                                    <td class="text-center">
                                        {{ optional($color->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        @canModule('colors','update')
                                            <a href="{{ route('admin.colors.edit', $color->id) }}"
                                               class="btn btn-warning btn-xs">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcanModule

                                        @canModule('colors','delete')
                                            <form action="{{ route('admin.colors.destroy', $color->id) }}"
                                                  method="POST"
                                                  style="display:inline-block;"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa màu xe này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcanModule
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Chưa có màu xe nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
