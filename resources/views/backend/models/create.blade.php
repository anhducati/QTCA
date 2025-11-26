@extends('layouts.panel')

@section('main')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Thêm dòng xe</h2>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('admin.models.index') }}">Quản lý dòng xe</a></li>
            <li class="active"><strong>Thêm mới</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Thông tin dòng xe</h5>
                </div>
                <div class="ibox-content">
                    @include('layouts.message')

                    <form action="{{ route('admin.models.store') }}" method="POST">
                        @csrf
                        @php $model = null; @endphp

                        @include('backend.models._form')

                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i> Lưu
                        </button>
                        <a href="{{ route('admin.models.index') }}" class="btn btn-default">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
