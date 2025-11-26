@extends('layouts.client')

@section('main')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{config('apps.user.title') }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>

                <li class="active">
                    <strong>{{config('apps.user.title') }}</strong>
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
                        <h5>{{config('apps.user.heading') }}</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#">Config option 1</a>
                                </li>
                                <li><a href="#">Config option 2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                            <a href="" class="btn btn-danger"><i class="fa fa-plus"></i>  Thêm thành viên</a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables" >
                                <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                                    </th>
                                    <th>Avatar</th>
                                    <th>Họ tên</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($users) && is_object($users))
                                    @foreach($users as $user)
                                        <tr class="gradeA">
                                            <td>
                                                <input type="checkbox" value="" class="input-checkbox">
                                            </td>
                                            <td>
                                                {{ $user->name }}
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td class="text-center">
                                                <input type="checkbox" class="js-switch" checked />
                                            </td>
                                            <td class="text-center">
                                                <a href="" class="btn btn-warning" type="button"><i class="fa fa-edit"></i></a>
                                                <a href="" class="btn btn-danger" type="button"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif


                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>
                                        <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                                    </th>
                                    <th>Avatar</th>
                                    <th>Họ tên</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection

