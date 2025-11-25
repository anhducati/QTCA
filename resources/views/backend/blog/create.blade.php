@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.blog.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.blog.createTitle') }}</strong>
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
                        <h5>{{config('apps.blog.createTitle') }}</h5>
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
                                <form role="form" method="post" action="" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                    <div class="col-lg-9">
                                    <div class="form-group">
                                        <label>Tên bài viết</label>
                                        <input type="text" name="title"
                                               class="form-control"
                                               value="{{ old('title') }}"
                                                oninput="updateSlug(this.value)"
                                        >
                                        @error('title')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Tên bài viết En</label>
                                        <input type="text" name="title_en"
                                               class="form-control"
                                               value="{{ old('title_en') }}"
                                        >
                                        @error('title_en')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="category_id" class="form-control">
                                            @foreach($categories as $value)
                                                @if($value->id = 1)
                                                    <option value="{{ $value->id }}" selected >{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->id }}" >{{ $value->name }}</option>
                                                @endif

                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

{{--                                    <div class="form-group">--}}
{{--                                        <label>Image</label>--}}
{{--                                        <input type="file" name="image_file"--}}
{{--                                               class="form-control"--}}
{{--                                        >--}}
{{--                                        @error('image_file')--}}
{{--                                        <div class="error-danger">* {{ $message }}</div>--}}
{{--                                        @enderror--}}
{{--                                    </div>--}}
                                    
                                    </div>
                                    <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Image</label>
                                        <div id="image-container" style="position: relative;">
                                            <img src="{{ asset('upload/blog/df.jpg') }}" style="width: 400px" alt="" class="img-thumbnail" id="preview-image">
                                            <button type="button" id="change-image-btn" class="btn btn-soft-dark" style="display: block; width: 285px">
                                                Chọn ảnh đại diện bài viết
                                            </button>
                                            <input type="file" name="image_file" id="logo_path" class="form-control" style="display: none;" accept="image/*">
                                            @error('image_file')
                                            <div class="error-danger">* {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    </div>
                                </div>  
                                    <div class="form-group" style="display: none">
                                        <label>Đường dẫn (Link bài viết)</label>
                                        <input type="text" name="slug"
                                               placeholder="Slug"
                                               class="form-control"
                                                id="slug-input"
                                               value="{{ old('slug') }}" readonly
                                        >
                                        @error('slug')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="ibox-title">
                                            <h5>Nội dung bài viết</h5>
                                        </div>

                                        {{-- class="tinymce"   tinymce --}}
                                        <textarea style="height: 300px" style="width: 100%" class="tinymce"  name="contents" >
                                           {{ old('contents')}}
                                        </textarea>
                                        @error('contents')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="ibox-title">
                                            <h5>Nội dung bài viết En</h5>
                                        </div>

                                        {{-- class="tinymce"   tinymce --}}
                                        <textarea style="height: 300px" style="width: 100%" class="tinymce"  name="contents_en" >
                                           {{ old('contents_en')}}
                                        </textarea>
                                        @error('contents_en')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group tagsinput">
                                        <label >Tags</label>
                                        <input type="text" name="tags" id="tags" placeholder="Nhập tags" class="form-control"
                                               value="{{ old('tags') }}">
                                        @error('tags')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <label>Meta description</label>
                                        <textarea style="height: 100px"  name="meta_description" class="form-control">{{ old('meta_description') }}</textarea>

                                        @error('meta_description')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div><div class="form-group">
                                        <label>Meta keywords</label>
                                        <input type="text" name="meta_keyword" placeholder="Nhập Meta keywords" class="form-control"
                                               value="{{ old('meta_keyword') }}">
                                        @error('meta_keyword')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Publish *</label>
                                        <select name="is_publish" id="" class="form-control">
                                            <option value="1" selected>Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('is_publish')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>


                                    {{-- @if(Auth::user()->is_admin)
                                    <div class="form-group">
                                        <label>Status *</label>
                                        <select name="status" id="" class="form-control">
                                            <option value="1" selected>Active</option>
                                            <option value="0" >Inactive</option>
                                        </select>
                                        @error('status')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>
                                    @else
{{--                                        Trường hợp không phải admin ần status --}}
                                        {{-- <select name="status" hidden="" class="form-control" style="display: none">
                                            <option value="0" selected>Inactive</option>
                                        </select>
                                    @endif --}} 

                                    <div>
                                        <button class="btn btn-primary" type="submit"><strong>Thêm bài viết</strong></button>
                                        <a class="btn btn-danger" href="{{ route('admin.blog') }}">Danh sách bài viết</a>

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

    <!-- Tagsinput -->
    <link href="{{ asset('assets/backend/tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">

    <style>

        .tagsinput .label {
            display: inline-block;
            font-size: 15px;
            height: 25px;
        }
    </style>

    {{-- 
        API Key tinymce
        VD: ub9wru4c8z57hio4z5170jv0k4zik8cf4e6moqfnhktv4umz
        
    --}}
    <script src="https://cdn.tiny.cloud/1/ub9wru4c8z57hio4z5170jv0k4zik8cf4e6moqfnhktv4umz/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: '.tinymce',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],

            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
    </script>


    
    


@endsection

@section('page-scripts')

    <script src="{{ asset('assets/backend/tagsinput/bootstrap-tagsinput.js') }}"></script>

    <script>
        $("#tags").tagsinput();
    </script>

    {{-- // Chặn enter input tags --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lấy tất cả các trường nhập liệu trong biểu mẫu
            var formFields = document.querySelectorAll('input');

            // Lặp qua từng trường nhập liệu
            formFields.forEach(function(field) {
                // Lắng nghe sự kiện keydown trên mỗi trường nhập liệu
                field.addEventListener('keydown', function(event) {
                    // Nếu mã phím là 13 (mã cho phím Enter)
                    if (event.keyCode === 13) {
                        // Ngăn chặn hành động mặc định của nút Enter
                        event.preventDefault();
                        return false;
                    }
                });
            });

            // Lắng nghe sự kiện submit trên biểu mẫu
            document.querySelector('form').addEventListener('submit', function(event) {
                // Ngăn chặn hành động submit mặc định của biểu mẫu
                event.preventDefault();
                return false;
            });
        });
    </script>


    <script>
        // Lắng nghe sự kiện click vào container chứa ảnh và nút thay đổi
        document.getElementById('image-container').addEventListener('click', function() {
            // Kích hoạt sự kiện click cho input file
            document.getElementById('logo_path').click();
        });

        // Lắng nghe sự kiện khi có sự thay đổi trong input file
        document.getElementById('logo_path').addEventListener('change', function() {
            // Kiểm tra xem người dùng đã chọn file chưa
            if (this.files && this.files[0]) {
                var file = this.files[0];
                var fileType = file.type;

                // Kiểm tra xem tệp được chọn có phải là hình ảnh không
                if (!fileType.startsWith('image/')) {
                    alert('Vui lòng chọn một tệp hình ảnh.');
                    return;
                }

                var reader = new FileReader();

                // Đọc và hiển thị ảnh mới
                reader.onload = function(e) {
                    document.getElementById('preview-image').setAttribute('src', e.target.result);
                }

                reader.readAsDataURL(file);
            }
        });
    </script>
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

