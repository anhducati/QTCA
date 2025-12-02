@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ config('apps.user.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>{{ config('apps.user.updateTitle') }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{ config('apps.user.updateTitle') }}</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                        </div>
                    </div>

                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12 b-r">
                                @include('layouts.message')

                                <form role="form"
                                      method="POST"
                                      action="{{ route('admin.user.update', $user->id) }}">
                                    @csrf
                                    @method('PUT')

                                    {{-- THÔNG TIN CƠ BẢN --}}
                                    <div class="form-group">
                                        <label>Họ tên</label>
                                        <input type="text"
                                               name="name"
                                               placeholder="Nhập họ tên"
                                               class="form-control"
                                               value="{{ old('name', $user->name) }}">
                                        @error('name')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text"
                                               name="email"
                                               placeholder="Nhập email"
                                               class="form-control"
                                               value="{{ old('email', $user->email) }}">
                                        @error('email')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Vai trò</label>
                                        <select name="is_admin" class="form-control">
                                            <option value="1"
                                                {{ (int)old('is_admin', $user->is_admin) === 1 ? 'selected' : '' }}>
                                                Quản trị viên (T1)
                                            </option>
                                            <option value="0"
                                                {{ (int)old('is_admin', $user->is_admin) === 0 ? 'selected' : '' }}>
                                                Người dùng thường (T2/T3)
                                            </option>
                                        </select>
                                        @error('is_admin')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                        <span class="help-block m-t-xs">
                                            <i class="fa fa-info-circle text-info"></i>
                                            Quản trị viên (T1) luôn có toàn quyền, bảng phân quyền bên dưới chủ yếu dùng cho người dùng thường.
                                        </span>
                                    </div>

                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select name="status" class="form-control">
                                            <option value="1"
                                                {{ (int)old('status', $user->status) === 1 ? 'selected' : '' }}>
                                                Kích hoạt
                                            </option>
                                            <option value="0"
                                                {{ (int)old('status', $user->status) === 0 ? 'selected' : '' }}>
                                                Không kích hoạt
                                            </option>
                                        </select>
                                        @error('status')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>
                                            Mật khẩu mới
                                            <span class="text-danger">(Nhập nếu muốn thay đổi)</span>
                                        </label>
                                        <input type="password"
                                               name="password"
                                               placeholder="Nhập mật khẩu mới"
                                               class="form-control"
                                               value="{{ old('password') }}">
                                        @error('password')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>

                                    {{-- PHÂN QUYỀN MENU & CHỨC NĂNG --}}
                                    <h3 class="m-b-md">Phân quyền menu & chức năng</h3>

                                    <div class="alert alert-info">
                                        <i class="fa fa-shield"></i>
                                        <strong>Lưu ý:</strong>
                                        Nếu người dùng là <b>Quản trị viên (T1)</b>, hệ thống sẽ bỏ qua bảng phân quyền này và cho phép truy cập toàn bộ.
                                        Các quyền bên dưới áp dụng cho <b>người dùng thường</b>.
                                    </div>

                                    @php
                                        $modules = [
                                            'brands'                => 'Hãng xe',
                                            'models'                => 'Dòng xe',
                                            'colors'                => 'Màu xe',
                                            'warehouses'            => 'Kho',
                                            'suppliers'             => 'Nhà cung cấp',
                                            'customers'             => 'Khách hàng',
                                            'vehicles'              => 'Xe',
                                            'import_receipts'       => 'Phiếu nhập',
                                            'export_receipts'       => 'Phiếu xuất',
                                            'payments'              => 'Phiếu thu',
                                            'stock_takes'           => 'Kiểm kê',
                                            'inventory_adjustments' => 'Điều chỉnh tồn',
                                            'inventory_logs'        => 'Nhật ký tồn kho',
                                        ];

                                        // Nếu validate lỗi, ưu tiên dữ liệu old('permissions') để tích lại checkbox
                                        $oldPermissions = old('permissions', []);
                                    @endphp

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Module</th>
                                                <th class="text-center">Xem</th>
                                                <th class="text-center">Thêm</th>
                                                <th class="text-center">Sửa</th>
                                                <th class="text-center">Xóa</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($modules as $key => $label)
                                                @php
                                                    $perm = $user->modulePermissions->firstWhere('module_key', $key);

                                                    $hasOld = !empty($oldPermissions);
                                                @endphp
                                                <tr>
                                                    <td>{{ $label }}</td>

                                                    @foreach(['read','create','update','delete'] as $act)
                                                        @php
                                                            if ($hasOld) {
                                                                $checked = !empty($oldPermissions[$key][$act]);
                                                            } else {
                                                                $checked = $perm ? (bool)$perm->{'can_'.$act} : false;
                                                            }
                                                        @endphp
                                                        <td class="text-center">
                                                            <input type="checkbox"
                                                                   name="permissions[{{ $key }}][{{ $act }}]"
                                                                   value="1"
                                                                   {{ $checked ? 'checked' : '' }}>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="m-t-md">
                                        <button class="btn btn-primary" type="submit">
                                            <strong>Cập nhật tài khoản</strong>
                                        </button>
                                        <a class="btn btn-danger" href="{{ route('admin.user.index') }}">
                                            Danh sách tài khoản
                                        </a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection

@section('page-css')
    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">
@endsection

@section('page-scripts')
    {{-- Nếu không dùng summernote ở trang này có thể bỏ 2 dòng dưới để nhẹ hơn --}}
    <script src="{{ asset('assets/backend/js/bootstrap.min.js') }}"></script>
@endsection
