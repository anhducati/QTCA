<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">

            {{-- LẤY THÔNG TIN USER --}}
            @php
                /** @var \App\Models\User $user */
                $user = Auth::user();

                $avatar = !empty($user->image)
                    ? $user->image
                    : asset('upload/user-default.png');

                // Quyền theo module (đã định nghĩa trong User::canModule)
                $canBrands        = $user->canModule('brands', 'read');
                $canModels        = $user->canModule('models', 'read');
                $canColors        = $user->canModule('colors', 'read');

                $canWarehouses    = $user->canModule('warehouses', 'read');
                $canSuppliers     = $user->canModule('suppliers', 'read');
                $canCustomers     = $user->canModule('customers', 'read');

                $canVehicles      = $user->canModule('vehicles', 'read');

                $canImport        = $user->canModule('import_receipts', 'read');
                $canExport        = $user->canModule('export_receipts', 'read');
                $canPayments      = $user->canModule('payments', 'read');

                $canStockTakes    = $user->canModule('stock_takes', 'read');
                $canAdjustments   = $user->canModule('inventory_adjustments', 'read');

                $canInventoryLogs = $user->canModule('inventory_logs', 'read');
            @endphp

            {{-- HEADER PROFILE --}}
            <li class="nav-header">
                <div class="dropdown profile-element text-center">
                    <span>
                        <img alt="image"
                             class="img-circle"
                             src="{{ $avatar }}"
                             style="width: 48px; height: 48px; object-fit: cover;">
                    </span>

                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">
                                    {{ $user->name ?? 'Người dùng' }}
                                </strong>
                            </span>
                            <span class="text-muted text-xs block">
                                @if(!empty($user->role_label))
                                    {{ $user->role_label }}
                                @else
                                    @if(!empty($user->is_admin) && $user->is_admin)
                                        Quản trị viên
                                    @else
                                        Nhân viên
                                    @endif
                                @endif
                                <b class="caret"></b>
                            </span>
                        </span>
                    </a>

                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#changePasswordModal">
                                Đổi mật khẩu
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('admin.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                                Đăng xuất
                            </a>
                            <form id="logout-form-sidebar" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="logo-element">
                    CA+
                </div>
            </li>

            {{-- DASHBOARD (luôn cho phép vào) --}}
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="">
                    <i class="fa fa-dashboard"></i>
                    <span class="nav-label">Bảng điều khiển</span>
                </a>
            </li>

            {{-- QUẢN LÝ NGƯỜI DÙNG (chỉ admin T1) --}}
            @if(!empty($user->is_admin) && $user->is_admin)
                <li class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span class="nav-label">Người dùng</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse {{ request()->routeIs('admin.user.*') ? 'in' : '' }}">
                        <li class="{{ request()->routeIs('admin.user.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.user.index') }}">Danh sách người dùng</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.user.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.user.create') }}">Thêm người dùng</a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- ===================== DANH MỤC XE ===================== --}}
            @if($canBrands || $canModels || $canColors)
                <li class="{{ request()->routeIs('admin.brands.*','admin.models.*','admin.colors.*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-car"></i>
                        <span class="nav-label">Danh mục xe</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse
                        {{ request()->routeIs('admin.brands.*','admin.models.*','admin.colors.*') ? 'in' : '' }}">

                        @if($canBrands)
                            <li class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.brands.index') }}">Hãng xe</a>
                            </li>
                        @endif

                        @if($canModels)
                            <li class="{{ request()->routeIs('admin.models.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.models.index') }}">Dòng xe</a>
                            </li>
                        @endif

                        @if($canColors)
                            <li class="{{ request()->routeIs('admin.colors.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.colors.index') }}">Màu xe</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

            {{-- ===================== KHO & ĐỐI TÁC ===================== --}}
            @if($canWarehouses || $canSuppliers || $canCustomers)
                <li class="{{ request()->routeIs('admin.warehouses.*','admin.suppliers.*','admin.customers.*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-building"></i>
                        <span class="nav-label">Kho & Đối tác</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse
                        {{ request()->routeIs('admin.warehouses.*','admin.suppliers.*','admin.customers.*') ? 'in' : '' }}">

                        @if($canWarehouses)
                            <li class="{{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.warehouses.index') }}">Kho</a>
                            </li>
                        @endif

                        @if($canSuppliers)
                            <li class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.suppliers.index') }}">Nhà cung cấp</a>
                            </li>
                        @endif

                        @if($canCustomers)
                            <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.customers.index') }}">Khách hàng</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif


            {{-- ===================== BÁN LẺ XE ===================== --}}
                @php
                    $canRetail = $user->canModule('vehicle_sales', 'read');
                @endphp

                @if($canRetail)
                    <li class="{{ request()->routeIs('admin.vehicle_sales.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.vehicle_sales.index') }}">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="nav-label">Bán lẻ xe</span>
                        </a>
                    </li>
                @endif


            {{-- ===================== DANH SÁCH XE THEO SỐ KHUNG ===================== --}}
            @if($canVehicles)
                <li class="{{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.vehicles.index') }}">
                        <i class="fa fa-list-alt"></i>
                        <span class="nav-label">Danh sách xe (số khung)</span>
                    </a>
                </li>
            @endif

            {{-- ===================== NGHIỆP VỤ KHO ===================== --}}
            @if($canImport || $canExport || $canPayments)
                <li class="{{ request()->routeIs('admin.import_receipts.*','admin.export_receipts.*','admin.payments.*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-exchange"></i>
                        <span class="nav-label">Nghiệp vụ kho</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse
                        {{ request()->routeIs('admin.import_receipts.*','admin.export_receipts.*','admin.payments.*') ? 'in' : '' }}">

                        @if($canImport)
                            <li class="{{ request()->routeIs('admin.import_receipts.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.import_receipts.index') }}">Phiếu nhập kho</a>
                            </li>
                        @endif

                        @if($canExport)
                            <li class="{{ request()->routeIs('admin.export_receipts.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.export_receipts.index') }}">Phiếu xuất kho / Bán xe</a>
                            </li>
                        @endif

                        @if($canPayments)
                            <li class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.payments.index') }}">Phiếu thu tiền khách</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

            {{-- ===================== KIỂM KÊ & ĐIỀU CHỈNH ===================== --}}
            @if($canStockTakes || $canAdjustments)
                <li class="{{ request()->routeIs('admin.stock_takes.*','admin.inventory_adjustments.*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-check-square-o"></i>
                        <span class="nav-label">Kiểm kê & Điều chỉnh</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse
                        {{ request()->routeIs('admin.stock_takes.*','admin.inventory_adjustments.*') ? 'in' : '' }}">

                        @if($canStockTakes)
                            <li class="{{ request()->routeIs('admin.stock_takes.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.stock_takes.index') }}">Phiếu kiểm kê kho</a>
                            </li>
                        @endif

                        @if($canAdjustments)
                            <li class="{{ request()->routeIs('admin.inventory_adjustments.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.inventory_adjustments.index') }}">Phiếu điều chỉnh tồn</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif

            {{-- ===================== NHẬT KÝ KHO ===================== --}}
            @if($canInventoryLogs)
                <li class="{{ request()->routeIs('admin.inventory_logs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.inventory_logs.index') }}">
                        <i class="fa fa-history"></i>
                        <span class="nav-label">Nhật ký tồn kho</span>
                    </a>
                </li>
            @endif

            {{-- ===================== BLOG / TIN TỨC (nếu cần giữ) =====================
            @if(!empty($user->is_admin) && $user->is_admin)
                <li class="{{ request()->routeIs('admin.category.*','admin.blog*','admin.major*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-newspaper-o"></i>
                        <span class="nav-label">Nội dung website</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse
                        {{ request()->routeIs('admin.category.*','admin.blog*','admin.major*') ? 'in' : '' }}">
                        <li class="{{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.category.index') }}">Danh mục bài viết</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.blog*') ? 'active' : '' }}">
                            <a href="{{ route('admin.blog') }}">Bài viết</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.major*') ? 'active' : '' }}">
                            <a href="{{ route('admin.major') }}">Ngành đào tạo</a>
                        </li>
                    </ul>
                </li>
            @endif --}}

        </ul>
    </div>
</nav>

{{-- MODAL ĐỔI MẬT KHẨU (giữ nguyên của anh) --}}
<div class="modal inmodal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated fadeInDown">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Đổi mật khẩu</h4>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="form-group">
                        <label for="old_password">Mật khẩu hiện tại</label>
                        <input type="password" name="old_password" id="old_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                    <button type="button" class="btn btn-primary btn-block" id="changePasswordBtn">
                        Đổi mật khẩu
                    </button>
                </form>
                <div id="changePasswordMsg" class="m-t-sm"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('#changePasswordBtn').on('click', function () {
            var $btn = $(this);
            var $form = $('#changePasswordForm');
            var $msg = $('#changePasswordMsg');

            $btn.prop('disabled', true).text('Đang xử lý...');
            $msg.html('');

            $.ajax({
                url: '{{ route('admin.user.changepassword') }}',
                method: 'POST',
                data: $form.serialize(),
                success: function () {
                    $msg.html('<div class="alert alert-success">Đổi mật khẩu thành công.</div>');
                    $form[0].reset();
                },
                error: function (xhr) {
                    var text = 'Có lỗi xảy ra. Vui lòng thử lại.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        text = xhr.responseJSON.message;
                    }
                    $msg.html('<div class="alert alert-danger">' + text + '</div>');
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Đổi mật khẩu');
                }
            });
        });
    });
</script>
@endpush
