@extends('layouts.base')

@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/js/plugin/selectpicker/css/bootstrap-select.min.css')}}">
@endsection

@section('content')
    <div class="container">
        <div class="page-inner order-page profile-page">
            <div class="page-header">
                <h4 class="page-title">{{isset($data)?__('edit_restaurant'):__('create_restaurant')}}</h4>
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
                        <a href="{{ route('admin.restaurant.list') }}">@lang('restaurant')</a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">{{isset($data)?__('edit'):__('create')}}</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="card-title">@lang('information')</div>
                        </div>
                        <form id="submitForm" method="post" action="{{route('admin.restaurant.store')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{isset($data)?$data->id:0}}">
                            <div class="card-body">
                                @if(session()->has('server_error'))
                                    <div class="custom-error">
                                        <h5 class="text-center">@lang('server_error') @lang('try_again')</h5>
                                    </div>
                                @endif
                                <div class="form-group form-show-validation row">
                                    <label for="restaurant_name" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('restaurant_name')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" value="{{isset($data)?$data->name:''}}" required>
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row">
                                    <label for="slogan" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('slogan')</label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="text" class="form-control" id="slogan" name="slogan" value="{{isset($data)?$data->slogan:''}}">
                                    </div>
                                </div>
                                {{-- <div class="form-group form-show-validation row">
                                    <label for="tax_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('tax_id')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{isset($data)?$data->tax_id:''}}" required>
                                    </div>
                                </div> --}}
                                <div class="form-group form-show-validation row">
                                    <label for="rut" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('rut')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="text" class="form-control" id="rut" name="rut" value="{{isset($data)?$data->rut:''}}" required>
                                    </div>
                                </div>

                                <div class="form-group form-show-validation row">
                                    <label for="address" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('address')</label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="text" class="form-control" id="address" name="address" value="{{isset($data)?$data->address:''}}">
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row">
                                    <label for="bank_transfer_details" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('bank_transfer_details')</label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <textarea class="form-control" id="bank_transfer_details" name="bank_transfer_details" rows="5">{{isset($data)?$data->bank_transfer_details:''}}</textarea>
                                    </div>
                                </div>

                                <div class="form-dgroup form-show-validation row">
                                    <label class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right font-weight-bold">@lang('is_receipt_sii')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <div class="form-check form-check-inline pt-0">
                                          <input class="form-check-input" type="radio" name="is_receipt_sii" id="is_receipt_sii1" value="1" {{ isset($data) && $data->is_receipt_sii==true ? 'checked':''}}>
                                          <label class="form-check-label pt-2" for="is_receipt_sii1">@lang('is_format_sii')</label>
                                        </div>
                                        <div class="form-check form-check-inline pt-0">
                                          <input class="form-check-input" type="radio" name="is_receipt_sii" id="is_receipt_sii0" value="0" {{ isset($data) && $data->is_receipt_sii==false ? 'checked':''}}>
                                          <label class="form-check-label pt-2" for="is_receipt_sii0">@lang('is_format_generic')</label>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group form-show-validation row">
                                    <label for="owner_id" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('owner')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <select class="form-control" id="owner_id" name="owner_id" required>
                                            <option value="" selected disabled></option>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}" {{isset($data)&&$data->owner_id==$user->id?'selected':''}}>{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-show-validation row">
                                    <label for="users" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('admins')</label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <select class="form-control selectpicker" id="users" name="users[]" multiple>
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}" {{isset($admins)&&in_array($user->id, $admins)?'selected':''}}>{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-black" type="submit">@lang('save')</button>
                                        <a href="{{route('admin.restaurant.list')}}" class="btn">@lang('cancel')</a>
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
    <script src="{{asset('custom/js/admin/restaurant.js')}}?v=202112061555"></script>
@endsection
