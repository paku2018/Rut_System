@extends('layouts.base')

@section('page-css')

@endsection

@section('content')
    <div class="container">
        <div class="page-inner order-page profile-page">
            <div class="page-header">
                <h4 class="page-title">@lang('my_profile')</h4>
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
                        <a href="{{ route('profile') }}">@lang('profile')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="card-title">@lang('information')</div>
                        </div>
                        <form id="submitForm" method="post" action="{{route('profile.update')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                @if(session()->has('update_success'))
                                    <div class="">
                                        <h4 class="text-center text-success">@lang('profile_updated')</h4>
                                    </div>
                                @endif
                                @if(session()->has('server_error'))
                                    <div class="custom-error">
                                        <h5 class="text-center">@lang('server_error') @lang('try_again')</h5>
                                    </div>
                                @endif

                                <div class="form-group form-show-validation row">
                                    <label for="name" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('name')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="text" class="form-control" id="name" name="name" value="{{isset($data)?$data->name:''}}" required>
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row">
                                    <label for="email" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('email')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="email" class="form-control" id="email" name="email" value="{{isset($data)?$data->email:''}}" disabled>
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row">
                                    <div class="col-12 offset-sm-3">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" value="1" id="change_password" name="change_password" onchange="showNewPassword()">
                                                <span class="form-check-sign">@lang('change_password')</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row new_password">
                                    <label for="new_password" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('new_password')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row new_password">
                                    <label for="confirm_password" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('confirm_password')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-black" type="submit">@lang('save')</button>
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
    <script src="{{asset('custom/js/profile.js')}}?v=202112061555"></script>
@endsection
