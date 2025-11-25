<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <div class="d-flex">
                        <span>
                            @if(!empty(Auth::user()->image))
                                <img alt="image" style="width: 30px" class="img-circle" src="{{ Auth::user()->image }}" />
                            @else
                                <img alt="image" style="width: 30px" class="img-circle" src="{{ asset('upload/user-default.png') }}" />
                            @endif
                            <strong class="font-bold">{{ Auth::user()->name }}</strong>
                        </span>
                    </div>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs">
                        </span> <span class="text-muted text-xs block">Quản trị <b class="caret"></b></span> </span> 
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li>
                            <a href="#" data-toggle="modal" data-target="#changePasswordModal">Thay đổi mật khẩu</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="{{ route('admin.logout') }}">Đăng xuất</a></li>
                    </ul>
                </div>
                <div class="logo-element"> 
                   <img src="{{ asset('assets/backend/img/Logo_TL.png') }}" alt="TLU" style="width: 40px">
                </div>
            </li>

            @if(Auth::user()->is_admin)
                <li @if(session()->get('active') == 'user') class="active" @endif>
                    <a href="{{ route('admin.user.index') }}"><i class="fa fa-user-circle"></i> <span class="nav-label">Thành viên</span></a>
                </li>
            @endif

            <li @if(session()->get('active') == 'category') class="active" @endif>
                <a href="{{ route('admin.category.index') }}"><i class="fa fa-bars"></i> <span class="nav-label">Danh mục</span></a>
            </li>

            <li @if(session()->get('active') == 'blog') class="active" @endif>
                <a href="{{ route('admin.blog') }}"><i class="fa fa-book"></i> <span class="nav-label">Bài viết</span></a>
            </li>

            <li @if(session()->get('active') == 'major') class="active" @endif>
                <a href="{{ route('admin.major') }}"><i class="fa fa-book"></i> <span class="nav-label">Ngành đào tạo</span></a>
            </li>

            <li><a href="{{ route('admin.logout') }}">
                <i class="fa fa-sign-out"></i> <span class="nav-label">Đăng xuất</span>
            </a></li>
        </ul>
    </div>
</nav>

<!-- Modal thay đổi mật khẩu -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="height: 30px; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title" id="changePasswordModalLabel">Thay đổi mật khẩu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="form-group">
                        <label for="current-password">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" id="current-password" class="form-control">
                        <div class="text-danger" id="current-password-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu mới</label>
                        <input type="password" name="password" id="password" class="form-control">
                        <div class="text-danger" id="password-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                    <button type="button" class="btn btn-primary" id="changePasswordBtn">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- @if(session('msg-success'))
    <div class="alert alert-success">
        {{ session('msg-success') }}
    </div>
@endif --}}
