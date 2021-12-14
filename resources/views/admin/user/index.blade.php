@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">@lang('user') @lang('list')</h4>
                <ul class="breadcrumbs">
                    <li class="nav-home">
                        <a href="#">
                            <i class="flaticon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">@lang('user')</a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.user.list') }}">@lang('list')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('information')</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt_table" class="display table table-striped table-hover" >
                                    <thead>
                                    <tr>
                                        <th>@lang('ID')</th>
                                        <th>@lang('name')</th>
                                        <th>@lang('email')</th>
                                        <th>@lang('taco_id')</th>
                                        <th class="text-center">@lang('action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->email}}</td>
                                            <td>{{$user->taco_user_id}}</td>
                                            <td class="text-center">
                                                <div class="form-button-action">
                                                    <a href="{{ route('admin.user.edit', $user->id) }}" type="button" class="btn btn-link btn-primary btn-lg">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" onclick="delUser({{$user->id}})" class="btn btn-link btn-danger">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <!-- Datatable JS -->
    <script src="{{asset('assets/js/plugin/datatables/datatables.min.js')}}"></script>
    <script>
        var path_delete = '{{ route('admin.user.delete') }}';
        var _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/admin/user-list.js')}}?v=202112061555"></script>
@endsection
