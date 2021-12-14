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
                        <a href="{{ route('restaurant.categories.list') }}">@lang('categories')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="card-title">{{isset($data)?__('edit_category'):__('create_category')}}</div>
                        </div>
                        <form id="submitForm" method="post" action="{{route('restaurant.categories.store')}}">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{isset($data)?$data->id:0}}">
                            <div class="card-body">
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
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-black" type="submit">@lang('save')</button>
                                        <a href="{{route('restaurant.categories.list')}}" class="btn">@lang('cancel')</a>
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
    <script src="{{asset('custom/js/resAdmin/category.js')}}?v=202112061555"></script>
@endsection
