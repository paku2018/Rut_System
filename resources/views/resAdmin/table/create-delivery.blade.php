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
                        <a href="{{ route('restaurant.tables.list') }}">@lang('tables')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="card-title">@lang('create_delivery')</div>
                        </div>
                        <form id="submitForm" method="post" action="{{route('restaurant.tables.store')}}">
                            @csrf
                            <input type="hidden" name="restaurant_id" value="{{$restaurant->id}}">
                            <div class="card-body">
                                @if(session()->has('server_error'))
                                    <div class="custom-error">
                                        <h5 class="text-center">@lang('server_error') @lang('try_again')</h5>
                                    </div>
                                @endif
                                <div class="form-group form-show-validation row">
                                    <label for="email" class="col-lg-3 col-md-3 col-sm-4 mt-sm-2 text-sm-right">@lang('client_email')<span class="required-label">*</span></label>
                                    <div class="col-lg-6 col-md-9 col-sm-8">
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="dt_table">
                                            <thead>
                                            <tr>
                                                <th>@lang('image')</th>
                                                <th>@lang('name')</th>
                                                <th>@lang('sale_price')</th>
                                                <th>@lang('count')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($products as $product)
                                                <tr>
                                                    <td>
                                                        @if($product->image)
                                                            <img src="{{$product->image}}" alt="no_img" class="preview-image">
                                                        @endif
                                                    </td>
                                                    <td>{{$product->name}}</td>
                                                    <td>{{number_format($product->sale_price,2)}}</td>
                                                    <td>
                                                        <div class="quantity">
                                                            <button class="minus-btn" type="button" name="button">-</button>
                                                            <input type="text" class="order_count" name="order_count_{{$product->id}}" data-value="{{$product->id}}" data-price="{{$product->sale_price}}" value="0" min="0">
                                                            <button class="plus-btn" type="button" name="button">+</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <h2 class="text-right">@lang('total') : <span id="new-total">0</span></h2>
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-black btn-order" type="button">@lang('save')</button>
                                        <a href="{{route('restaurant.tables.list')}}" class="btn">@lang('cancel')</a>
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
    <script>
        let path_create_delivery = '{{route('restaurant.tables.store-delivery')}}'
        let path_table = '{{ route('restaurant.tables.list') }}'
        let _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/resAdmin/table.js')}}?v=202112061555"></script>
@endsection
