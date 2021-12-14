@extends('layouts.base')
@section('page-css')
    <style>
        .table-box{
            cursor: pointer;
        }
        #order-list td{
            height: 40px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">@lang('tables')</h4>
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
                        <a href="{{ route('waiter.tables') }}">@lang('tables')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('tables')</h4>
                        </div>
                        <div class="card-body">
                            @if(count($tables) > 0)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-group">
                                            @foreach($tables as $table)
                                                <div class="table-box" data-index="{{$table->id}}">
                                                    <div class="table-status {{$table->status=="open"?'bg-success-gradient success-shadow':'bg-warning-gradient'}}" title="{{$table->status=="open"?__('open'):__('closed')}}"></div>
                                                    <h6 class="text-center mb-0">@lang('table')-{{$table->t_number}}</h6>
                                                    <h5 class="text-center">{{$table->name}}</h5>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h3 class="text-danger text-center">@lang('no_table')</h3>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="optionModal" tabindex="-1" role="dialog" aria-labelledby="optionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="optionModalLabel">@lang('select_order_type')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
                        <label>@lang('order_type')</label><br/>
                        <label class="form-radio-label">
                            <input class="form-radio-input" type="radio" name="orderType" value="0" checked>
                            <span class="form-radio-sign">@lang('new_order')</span>
                        </label>
                        <label class="form-radio-label ml-3">
                            <input class="form-radio-input" type="radio" name="orderType" value="1">
                            <span class="form-radio-sign">@lang('existing_order')</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-black btn-round btn-next">@lang('next')</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newModal" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="newModalLabel">@lang('create_orders')</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group d-flex align-items-center">
                                <label for="client" class="mb-0 mr-2">@lang('client')<span class="required-label">*</span></label>
                                <select class="form-control" id="client" name="client" style="max-width: 350px" onchange="checkCount()">
                                    <option value="" selected disabled>@lang('select')</option>
                                    @foreach($clients as $client)
                                        <option value="{{$client->id}}">{{$client->name."[".$client->email."]"}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group pb-0">
                                <label class="mb-0">@lang('products')<span class="required-label">*</span></label>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
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
                                                    <img src="{{$product->image}}" alt="no_img" class="preview-image">
                                                </td>
                                                <td>{{$product->name}}</td>
                                                <td>{{number_format($product->sale_price,2)}}</td>
                                                <td>
                                                    <div class="quantity">
                                                        <button class="minus-btn" type="button" name="button">-</button>
                                                        <input type="text" class="order_count" name="order_count_{{$product->id}}" data-value="{{$product->id}}" value="0" min="0">
                                                        <button class="plus-btn" type="button" name="button">+</button>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-black btn-round btn-order">@lang('save')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="existingModal" tabindex="-1" role="dialog" aria-labelledby="existingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="existingModalLabel">@lang('select_orders')</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="order-list">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-black btn-round btn-assign">@lang('save')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="detailModalLabel">@lang('detail')</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detail">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td>@lang('client_name')</td>
                                    <td>@lang('product')</td>
                                    <td>@lang('price')</td>
                                    <td>@lang('count')</td>
                                </tr>
                            </thead>
                            <tbody id="detail-body">

                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h4 class="text-right">@lang('total') : <span id="detail-total"></span></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-black btn-round btn-assign">@lang('save')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <!-- Datatable JS -->
    <script>
        let path_get_orders = '{{route('get-order-data')}}'
        let path_assign_orders = '{{route('assign-orders')}}'
        let path_table_info = '{{route('get-table-info')}}'
        let path_create_orders = '{{route('waiter.create-order')}}'
        let _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/waiter/table-list.js')}}"></script>
@endsection
