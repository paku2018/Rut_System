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
                        <a href="{{ route('restaurant.products.list') }}">@lang('products')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="card-title">{{isset($data)?__('edit_product'):__('create_product')}}</div>
                        </div>
                        <form id="submitForm" method="post" action="{{route('restaurant.products.store')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{isset($data)?$data->id:0}}">
                            <div class="card-body">
                                @if(session()->has('server_error'))
                                    <div class="custom-error">
                                        <h5 class="text-center">@lang('server_error') @lang('try_again')</h5>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group form-show-validation">
                                            <label for="name">@lang('name')<span class="required-label">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{isset($data)?$data->name:''}}" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group form-show-validation">
                                            <label for="category_id">@lang('category')<span class="required-label">*</span></label>
                                            <select class="form-control" id="category_id" name="category_id">
                                                <option value="" selected disabled>@lang('select')</option>
                                                @foreach($categories as $one)
                                                    <option value="{{$one->id}}" {{isset($data)&&$data->category_id==$one->id?'selected':''}}>{{$one->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group form-show-validation">
                                            <label for="purchase_price">@lang('purchase_price')<span class="required-label">*</span></label>
                                            <input type="number" min="0" class="form-control" id="purchase_price" name="purchase_price" value="{{isset($data)?$data->purchase_price:''}}" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group form-show-validation">
                                            <label for="sale_price">@lang('sale_price')<span class="required-label">*</span></label>
                                            <input type="number" min="0" class="form-control" id="sale_price" name="sale_price" value="{{isset($data)?$data->sale_price:''}}" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group form-show-validation">
                                            <label for="desc">@lang('description')<span class="required-label">*</span></label>
                                            <textarea class="form-control" id="desc" name="desc" required>{{isset($data)?$data->desc:''}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group form-show-validation">
                                            <label for="image">@lang('product_image')<span class="required-label">*</span></label>
                                            <div class="input-file input-file-image d-flex align-items-center">
                                                <img class="img-upload-preview" id="preview" width="100" height="100" src="{{isset($data->image)?$data->image:asset('assets/img/product-empty.png')}}" alt="preview" onerror="src='{{asset('assets/img/product-empty.png')}}'">
                                                <input type="file" class="form-control form-control-file" id="image" name="image" accept="image/*">
                                                <label for="image" class="btn btn-black btn-round btn-sm ml-3"><i class="fa fa-file-image"></i> @lang('upload')</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="sale_price" class="mr-3">@lang('availability')</label>
                                            <input type="checkbox" name="status" value="1" data-toggle="toggle" data-onstyle="primary" data-style="btn-round" {{!isset($data)||$data->status==1?'checked':''}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-black" type="submit">@lang('save')</button>
                                        <a href="{{route('restaurant.products.list')}}" class="btn">@lang('cancel')</a>
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
    <script src="{{asset('assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js')}}"></script>
    <script src="{{asset('custom/js/resAdmin/product.js')}}?v=202112061555"></script>
@endsection
