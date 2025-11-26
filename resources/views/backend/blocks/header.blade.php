<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            {{-- Nút thu gọn sidebar --}}
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#">
                <i class="fa fa-bars"></i>
            </a>

            {{-- Logo / Tên hệ thống --}}
            <span class="navbar-brand" style="font-weight: 600; letter-spacing: .5px;">
                CHUNG ANH
            </span>
        </div>

        <ul class="nav navbar-top-links navbar-right">

            {{-- Lời chào + tên user --}}
            <li>
                <span class="m-r-sm text-muted welcome-message">
                    Xin chào,
                    <strong>{{ Auth::user()->name ?? 'Người dùng' }}</strong>
                    @if(!empty(Auth::user()->role_label))
                        <span class="label label-info m-l-xs">
                            {{ Auth::user()->role_label }}
                        </span>
                    @else
                        @if(!empty(Auth::user()->is_admin) && Auth::user()->is_admin)
                            <span class="label label-danger m-l-xs">Quản trị</span>
                        @endif
                    @endif
                </span>
            </li>

            {{-- (Tuỳ chọn) chỗ cho thông báo sau này
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                    <i class="fa fa-bell"></i> <span class="label label-danger">3</span>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="#">
                            <div>
                                <i class="fa fa-car fa-fw"></i> Vừa nhập thêm 3 xe mới
                                <span class="pull-right text-muted small">3 phút trước</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            --}}

            {{-- Đăng xuất --}}
            <li>
                <a href="{{ route('admin.logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                    <i class="fa fa-sign-out"></i> Đăng xuất
                </a>
                <form id="logout-form-header" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>
</div>
