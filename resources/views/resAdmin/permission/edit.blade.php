@extends('resAdmin.res-layout.res-base')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/js/plugin/selectpicker/css/bootstrap-select.min.css')}}">
@endsection

@section('content')
    <div class="container">
        <div class="page-inner order-page profile-page">
            <div class="page-header">
                <h4 class="page-title">{{$restaurant->name}}</h4>
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
                        <a href="{{ route('restaurant.list') }}">@lang('restaurants')</a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('restaurant.permission.index') }}">@lang('permission')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="card-title">@lang('edit_permission')</div>
                        </div>
                        <form id="submitForm" method="post" action="{{route('restaurant.permission.store')}}">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                            <div class="card-body">
                                @if(session()->has('server_error'))
                                    <div class="custom-error">
                                        <h5 class="text-center">@lang('server_error') @lang('try_again')</h5>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label class="form-label">@lang('user') : {{$user->name}}</label>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('permission')</label>
                                    <div class="selectgroup selectgroup-pills w-100">
                                        @foreach($permissions as $permission)
                                            @php
                                                $checked = (isset($has_permissions) && in_array($permission->id, $has_permissions)) ? 'checked': '';
                                            @endphp
                                            <label class="selectgroup-item">
                                                <input type="checkbox" class="selectgroup-input" id="permission{{$permission->id}}" name="permission[]" value="{{$permission->id}}" {{$checked}}>
                                                <span class="selectgroup-button">{{$permission->title}}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-black" type="submit">@lang('save')</button>
                                        <a href="{{route('restaurant.permission.index')}}" class="btn">@lang('cancel')</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script src="{{asset('assets/js/plugin/selectpicker/js/bootstrap-select.min.js')}}"></script>
@endsection
