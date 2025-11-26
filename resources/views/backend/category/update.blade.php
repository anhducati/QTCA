@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.user.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.user.createTitle') }}</strong>
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
                        <h5>{{config('apps.user.createTitle') }}</h5>
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
                                <form role="form" method="post" action="">
                                    @method('PUT')
                                    @csrf
                                    <div class="form-group">
                                        <label>Tên danh mục</label>
                                        <input type="text" name="name"
                                               placeholder="Name"
                                               class="form-control"
                                               value="{{ old('name', $category->name) }}"
                                                oninput="updateSlug(this.value)"
                                               >
                                        @error('name')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Tên danh mục EN</label>
                                        <input type="text" name="name_en"
                                               placeholder="Name"
                                               class="form-control"
                                               value="{{ old('name_en', $category->name_en) }}"
                
                                               >
                                        @error('name_en')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group" style="display: none;">
                                        <label>Đường dẫn(Link danh mục)</label>
                                        <input type="text" name="slug"
                                               placeholder="Slug"
                                               class="form-control"
                                               id="slug-input"
                                               value="{{ old('slug', $category->slug) }}" readonly
                                        >
                                        @error('slug')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="title" placeholder="Nhập Meta title" class="form-control"
                                               value="{{ old('title', $category->title) }}">
                                        @error('title')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Meta title</label>
                                        <input type="text" name="meta_title" placeholder="Nhập Meta title" class="form-control"
                                               value="{{ old('meta_title', $category->meta_title) }}">
                                        @error('meta_title')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Meta description</label>
                                        <input type="text" name="meta_description" placeholder="Nhập Description title" class="form-control"
                                               value="{{ old('meta_description', $category->meta_description) }}">
                                        @error('meta_description')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div><div class="form-group">
                                        <label>Meta keywords</label>
                                        <input type="text" name="meta_keyword" placeholder="Nhập Meta keywords" class="form-control"
                                               value="{{ old('meta_keyword', $category->meta_keyword) }}">
                                        @error('meta_keyword')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Hiển thị menu</label>
                                        <select name="is_menu" id="" class="form-control" value="{{ old('is_menu') }}">
                                            @if($category->is_menu == 2)
                                                <option value="2" selected>Menu con</option>
                                            @else
                                                <option value="2" >Menu con</option>
                                            @endif
                                            
                                            @if($category->is_menu == 1)
                                                <option value="1" selected>Menu chính</option>
                                            @else
                                                <option value="1" >Menu chính</option>
                                            @endif

                                            @if($category->is_menu == 0)
                                                <option value="0" selected>Không hiển thị</option>
                                            @else
                                                <option value="0">Không hiển thị</option>
                                            @endif

                                        </select>
                                        @error('is_menu')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" id="" class="form-control">
                                            <option value="1" {{ ($category->status == 1) ? 'selected' : '' }} >
                                                Active
                                            </option>
                                            <option value="0" {{ ($category->status == 0) ? 'selected' : '' }} >Inactive</option>
                                        </select>
                                        @error('status')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <button class="btn btn-primary" type="submit"><strong>Cập nhật danh mục</strong></button>
                                        <a class="btn btn-danger" href="{{ route('admin.category.index') }}">Danh sách danh mục</a>

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
<script>
    function updateSlug(value) {
        var slug = slugify(value);
        document.getElementById('slug-input').value = slug;
    }

    function slugify(text) {
        return text.toString().toLowerCase()
            .normalize('NFD')           // Chuẩn hóa chuỗi Unicode
            .trim()                     // Loại bỏ khoảng trắng ở đầu và cuối chuỗi
            .replace(/[\u0300-\u036f]/g, '') // Loại bỏ dấu thanh từ
            .replace(/[^a-z0-9 -]/g, '') // Loại bỏ các ký tự không phải chữ cái, số, dấu gạch ngang hoặc khoảng trắng
            .replace(/\s+/g, '-')       // Thay thế khoảng trắng bằng dấu gạch ngang
            .replace(/-+/g, '-')        // Đảm bảo không có nhiều hơn một dấu gạch ngang liên tiếp
            .replace(/^-|-$/g, '');     // Loại bỏ các dấu gạch ngang ở đầu và cuối chuỗi
    }
</script>
@endsection

