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
                                <div class="form-show-validation row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group d-flex align-items-center">
                                            <label for="email" class="mt-sm-2 text-sm-right mr-2">@lang('client_email')<span class="required-label">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="waiter@app.pagocash" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group d-flex align-items-center">
                                            <label for="name" class="mt-sm-2 text-sm-right mr-2">@lang('client_name')</label>
                                            <input type="text" class="form-control" id="name" name="name" value="" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="form-group d-flex align-items-center">
                                            <label for="address" class="mt-sm-2 text-sm-right mr-2">@lang('address')</label>
                                            <input type="text" class="form-control" id="address" name="address" value="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 col-md-3">
                                        <div class="bg-black text-white mb-3">
                                            <h3 class="mb-0 text-center">@lang('order_list')</h3>
                                        </div>
                                        <div class="custom-scroll" id="assigned-orders" style="overflow-y: auto;height: calc( 100% - 100px)">

                                        </div>
                                        <div class="mt-1 pb-0">
                                            <h1 class="text-right mb-0">@lang('total') : <span id="detail-total">0</span></h1>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <div>
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="dt_table">
                                                    <thead>
                                                    <tr>
                                                        <th>@lang('image')</th>
                                                        <th>@lang('sort')</th>
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
                                                            <td>{{$product->category->order}}</td>
                                                            <td>{{$product->name}}</td>
                                                            <td>{{number_format($product->sale_price,2)}}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="quantity">
                                                                        <button class="minus-btn" type="button" name="button">-</button>
                                                                        <input type="text" class="order_count" name="order_count_{{$product->id}}" data-value="{{$product->id}}" data-price="{{$product->sale_price}}" value="0" min="0">
                                                                        <button class="plus-btn" type="button" name="button">+</button>
                                                                    </div>
                                                                    @if(count($agg_products) > 0)
                                                                        <button type="button" class="btn btn-black btn-sm btn-round ml-3" data-toggle="collapse" data-target="#collapse_{{$product->id}}" aria-expanded="false" aria-controls="collapse">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>

                                                                @if(count($agg_products) > 0)
                                                                    <div class="collapse" id="collapse_{{$product->id}}">
                                                                        <div class="agg_product">
                                                                            @foreach($agg_products as $one)
                                                                                <div class="form-check py-0">
                                                                                    <label class="form-check-label">
                                                                                        <input class="form-check-input sub_order" name="sub_order_{{$product->id}}" type="checkbox" value="{{$one->id}}" data-price="{{$one->sale_price}}">
                                                                                        <span class="form-check-sign">{{$one->name. " : $" .number_format($one->sale_price, 0, ".", ",")}}</span>
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <h2 class="text-right">@lang('total') : <span id="new-total">0</span></h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <div class="d-none">
                                            <span id="sprint_active" class="mr-auto text-success px-4 py-2  d-none" data-toggle="tooltip" data-placement="right" title="Impresora activa"><i class="fas fa-print fa-2x"></i> </span>
                                            <span id="sprint_inactive" class="mr-auto text-warning px-4 py-2" data-toggle="tooltip" data-placement="right" title="Impresora no disponible"><i class="fas fa-print fa-2x"></i> </span>
                                        </div>
                                        <button class="btn btn-black btn-round btn-print mr-3" type="button" disabled>@lang('print')</button>
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
    <script src="{{asset('assets/js/plugin/datatables/datatables.min.js')}}"></script>

    <script src="{{ asset('assets/js/print_tool_v2/zip.js/zip-full.min.js') }}"></script>
    <script src="{{ asset('assets/js/print_tool_v2/JSPrintManager.js') }}"></script>
    <script src="{{ asset('assets/js/print_tool_v2/bluebird.min.js') }}"></script>
    <script src="{{ asset('assets/js/utils_print.js') }}"></script>

    <script>
        let path_create_delivery = '{{route('restaurant.tables.store-delivery')}}'
        let path_table_info = '{{route('get-table-info')}}'
        let path_table = '{{ route('restaurant.tables.list') }}'
        let _token = '{{csrf_token()}}'
        let HOST_URL = "{{ url('/') }}"
    </script>
    <script src="{{asset('custom/js/resAdmin/table.js')}}?v=202203101555"></script>
@endsection
