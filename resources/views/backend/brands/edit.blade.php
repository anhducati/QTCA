{{-- resources/views/backend/brands/edit.blade.php --}}
@extends('backend.layouts.master')

@section('title', 'Sửa hãng xe')

@section('page-header')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Sửa hãng xe</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.brands.index') }}">Hãng xe</a></li>
                <li class="active"><strong>Sửa</strong></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">

        <div class="ibox">
            <div class="ibox-title">
                <h5>Sửa hãng xe</h5>
            </div>
            <div class="ibox-content">

                @include('backend.partials.flash')

                <form action="{{ route('admin.brands.update', $brand) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('backend.brands._form')

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-default">
                            Hủy
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
