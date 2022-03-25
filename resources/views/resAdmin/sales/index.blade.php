@extends('resAdmin.res-layout.res-base')

@section('content')
    <div class="container">
        <div class="page-inner">
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
                        <a href="#">@lang('sales_panel')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('sales_panel')</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-9">
                                    <div class="card">
                                        <div class="card-header bg-black-gradient">
                                            <h3 class="card-title text-white">
                                                @lang('sales')
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="product_code">@lang('add_with_product_code')</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="product_code" name="product_code" placeholder="@lang('input_code_product')">
                                                            <div class="input-group-append cursor-pointer">
                                                                <button class="btn btn-black" onclick="getProducts()">@lang('search')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="amount">@lang('amount')</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="amount" id="amount">
                                                            <div class="input-group-append cursor-pointer">
                                                                <button class="btn btn-black" onclick="addNormal()">@lang('add')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4 d-none" id="added_products">
                                                <div class="col-12">
                                                    <h4 class="mb-2">@lang('added_product')</h4>
                                                </div>
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                            <tr class="bg-warning">
                                                                <th>@lang('name')</th>
                                                                <th width="100">@lang('price')</th>
                                                                <th width="50">@lang('stock_count')</th>
                                                                <th width="150">@lang('quantity')</th>
                                                                <th width="100">@lang('total')</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="tb_added_products">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="card">
                                        <div class="card-header bg-black-gradient">
                                            <h3 class="card-title">
                                                @lang('sales_detail')
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="sale_user">@lang('client')</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="sale_user" placeholder="@lang('enter_rut')">
                                                            <div class="input-group-append cursor-pointer">
                                                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="document_type">@lang('document_type')</label>
                                                        <select class="form-control" id="document_type" name="document_type" ng-model="document_type">
                                                            <option value="">@lang('select')</option>
                                                            <option value="electronic_ticket">@lang('electronic_ticket')</option>
                                                            <option value="exempt_ballot">@lang('exempt_ballot')</option>
                                                            <option value="bill">@lang('bill')</option>
                                                            <option value="office_guide">@lang('office_guide')</option>
                                                            <option value="receipt">@lang('receipt')</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="payment_method">@lang('payment_method')</label>
                                                        <select class="form-control" id="payment_method" name="payment_method" ng-model="payment_method">
                                                            <option value="">@lang('select')</option>
                                                            <option value="app">@lang('app')</option>
                                                            <option value="cash">@lang('cash')</option>
                                                            <option value="transfer">@lang('transfer')</option>
                                                            <option value="debit_credit">@lang('debit_credit')</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sale_sum">@lang('total')</label>
                                                        <input type="text" class="form-control" id="sale_sum" ng-model="sale_sum" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="sale_comment">@lang('comment')(@lang('optional'))</label>
                                                        <textarea class="form-control" id="sale_comment" ng-model="sale_comment"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <button class="btn btn-primary btn-move" ng-click="save()">@lang('emit')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="productsModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="branch_title">@lang('products')</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive" id="product_list">
                                    <table id="dt_table" class="display table table-striped table-hover" >
                                        <thead>
                                            <tr>
                                                <th>@lang('name')</th>
                                                <th>@lang('price')</th>
                                                <th>@lang('stock_count')</th>
                                                <th>@lang('action')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <!-- Datatable JS -->
    <script src="{{asset('assets/js/plugin/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/moment/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        var _token = '{{csrf_token()}}'
        var path_get_products = '{{ route('restaurant.sales.get-products') }}'
    </script>
    <script src="{{asset('custom/js/resAdmin/inventory.js')}}?v=202203241555"></script>
@endsection
