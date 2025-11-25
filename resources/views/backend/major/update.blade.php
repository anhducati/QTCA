@extends('layouts.panel')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.major.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.major.updateTitle') }}</strong>
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
                        <h5>{{config('apps.major.updateTitle') }}</h5>
                        <div class="ibox-tools">

                            <a href="{{ route('admin.major.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>  Thêm ngành đào tạo </a>
                            <a href="{{ route('admin.major') }}" class="btn btn-danger"><i class="fa fa-bars"></i>  Danh sách </a>

                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            

                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12 b-r">
                                @include('layouts.message')
                                <form role="form" method="post" action="" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf

                                    <div class="row">
                                        <div class="col-lg-9">
                                    
                                            <div class="form-group">
                                                <label>Tên ngành</label>
                                                <input type="text" name="name"
                                                    placeholder="Tên ngành"
                                                    class="form-control"
                                                    value="{{ old('name', $major->name) }}"
                                                    oninput="updateSlug(this.value)"
                                                >
                                                @error('name')
                                                <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label>Tên ngành En</label>
                                                <input type="text" name="name_en"
                                                    placeholder="Tên ngành"
                                                    class="form-control"
                                                    value="{{ old('name_en', $major->name_en) }}"
                                                >
                                                @error('name_en')
                                                <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group" style="display: none;">
                                                <label>Đường dẫn (link)</label>
                                                <input type="text" name="slug"
                                                    placeholder="Đường dẫn"
                                                    class="form-control"
                                                      id="slug-input"
                                                    value="{{ old('slug', $major->slug) }}" readonly
                                                >
                                                @error('slug')
                                                <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label>Mã ngành</label>
                                                <input type="text" name="major_code" placeholder="Nhập mã ngành" class="form-control"
                                                    value="{{ old('major_code', $major->major_code) }}"
                                                >
                                                @error('major_code')
                                                <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div>
                                            

                                            {{-- <div class="form-group">
                                                <label for="subject_compo">Tổ hợp môn xét tuyển</label>
                                                <select id="subject_compo" name="subject_compo" data-placeholder="Chọn môn xét tuyển" multiple data-multi-select>
                                                    @if(isset($examBlocks) && is_object($examBlocks))
                                                        @foreach($examBlocks as $value)
                                                            @php
                                                                $selected = false;
                                                                foreach ($admissionSubject as $subject) {
                                                                    if ($subject->name == $value->name) {
                                                                        $selected = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            <option value="{{ $value->name }}" {{ $selected ? 'selected' : '' }}>
                                                                {{ $value->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('subject_compo')
                                                    <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div> --}}
                                            <div class="form-group">
                                                <label for="subject_compo">Tổ hợp môn xét tuyển</label>
                                                <select id="subject_compo" name="subject_compo" data-placeholder="Chọn môn xét tuyển" multiple data-multi-select>
                                                    @if(isset($examBlocks) && is_object($examBlocks))
                                                        @foreach($examBlocks as $value)
                                                            @php
                                                                $selected = false;
                                                                foreach ($admissionSubject as $subject) {
                                                                    if ($subject->exam_block_id == $value->id) {
                                                                        $selected = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            <option value="{{ $value->id }}" {{ $selected ? 'selected' : '' }}>
                                                                {{ $value->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('subject_compo')
                                                    <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            {{-- <div class="form-group">
                                                <label for="subject_compo">Tổ hợp môn xét tuyển</label>
                                                <select id="subject_compo" name="subject_comp" data-placeholder="Chọn môn xét tuyển" multiple data-multi-select>
                                                    @if(isset($examBlocks) && is_object($examBlocks))
                                                        @foreach($examBlocks as $examBlock)
                                                            @php
                                                                $selected = false;
                                                                foreach ($admissionSubject as $subject) {
                                                                    if ($subject->exam_block_id == $examBlock->id) {
                                                                        $selected = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp
                                                            <option value="{{ $examBlock->id }}" {{ $selected ? 'selected' : '' }}>
                                                                {{ $examBlock->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('subject_compo')
                                                    <div class="error-danger">* {{ $message }}</div>
                                                @enderror
                                            </div>
                                             --}}

                                        </div>

                                        <div class="col-lg-3">
                                             <div class="form-group">
                                                <label for="logo_path">Hình ảnh</label>
                                                <div id="image-container" style="position: relative;">
                                                    <img src="{{ asset('upload/major/' . $major->image_file) }}" style="width: 100%" alt="" class="img-thumbnail" id="preview-image">
                                                    <button type="button" id="change-image-btn" class="btn btn-soft-dark" style="display: block; width: 300px">
                                                        Thay đổi ảnh
                                                    </button>
                                                    <input type="file" name="image_file" id="logo_path" class="form-control" style="display: none;" accept="image/*">
                                                    @error('image_file')
                                                    <div class="error-danger">* {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                    

                                 
                                    

                                    <div class="form-group">
                                        <div class="ibox-title">
                                            <h5>Nội dung</h5>
                                        </div>

                                        {{-- class="tinymce"   tinymce --}}
                                        <textarea style="height: 300px" style="width: 100%" class="tinymce"  name="contents" >
                                           {{ old('contents', $major->content) }}
                                        </textarea>
                                        @error('contents')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="ibox-title">
                                            <h5>Nội dung En</h5>
                                        </div>

                                        {{-- class="tinymce"   tinymce --}}
                                        <textarea style="height: 300px" style="width: 100%" class="tinymce"  name="contents_en" >
                                           {{ old('contents_en', $major->content_en) }}
                                        </textarea>
                                        @error('contents_en')
                                        <div class="error-danger">* {{ $message }}</div>
                                        @enderror
                                    </div>

                                    

                                    <div>
                                        <button class="btn btn-primary" type="submit"><strong>Cập nhật</strong></button>
                                        <a class="btn btn-danger" href="{{ route('admin.major') }}">Danh sách ngành</a>

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

    {{-- multi-select --}}
    <link href="{{ asset('assets/backend/multi-select/multi-select.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/backend/css/customize.css') }}" rel="stylesheet">



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

    {{-- multi-select --}}
    <script src="{{ asset('assets/backend/multi-select/multi-select.js') }}"></script>

    

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

