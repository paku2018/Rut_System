@extends('layouts.base')
@section('page-css')
    <style>
        .table-box{
            cursor: pointer;
        }
        #order-list td{
            height: 50px;
        }
        @media (min-width: 768px){
            .modal-lg {
                max-width: 650px;
            }
        }
        @media (min-width: 992px){
            .modal-lg {
                max-width: 900px;
            }
        }
        @media (min-width: 1200px){
            .modal-lg {
                max-width: 1000px;
            }
        }
        .custom-scroll {
          scrollbar-color: #194af3 #e4e4e4;
          scrollbar-width: thin;
        }

        .custom-scroll::-webkit-scrollbar {
          width: 10px;
        }

        .custom-scroll::-webkit-scrollbar-track {
          background-color: #e4e4e4;
          border-radius: 100px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
          border-radius: 100px;
          border: 6px solid rgba(0, 0, 0, 0.18);
          border-left: 0;
          border-right: 0;
          background-color: #202f60;
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
                            <div>
                                <button class="btn btn-outline-primary btn-round btn-update mr-3">@lang('update_status')</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-group">

                                    </div>
                                </div>
                            </div>
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
                    <h2 class="modal-title" id="optionModalLabel">@lang('select_order_type')</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
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
        <div class="modal-dialog modal-lg-no" role="document" style="min-width:100%;position: absolute;bottom: 0;top: 0;left: 0;right: 0;margin:0;">
            <div class="modal-content" style="position: absolute;bottom: 0;top: 0;">
                <div class="modal-header pt-1 pb-0">
                    <h1 class="modal-title" id="detailModalLabel">@lang('detail')</h1>
                    <button type="button" class="close mt-1 pt-1" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" id="detail">
                    <div class="row">
                        <div class="col-12 col-md-4 pr-0">
                            <div class="bg-light px-2 py-0">
                                <div class="bg-black text-white px-3 py-1 mb-3">
                                    <h3 class="mb-0 text-center">@lang('order_list')</h3>
                                </div>
                                <div class="custom-scroll" id="assigned-orders" style="overflow-y: scroll;height: calc(88vh - 130px);padding-right: 10px;">

                                </div>
                                <div class="mt-1 pb-0">
                                    <h1 class="text-right mb-0">@lang('total') : <span id="detail-total"></span></h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8 pr-3">
                            <div class="bg-grey2 px-2 py-0 custom-scroll" style="overflow-y: scroll;height: calc(100vh - 130px);overflow-x: hidden;">
                                <div class="bg-black text-white px-3 py-1 mb-0">
                                    <h3 class="mb-0 text-center">@lang('add_order')</h3>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive pt-1">
                                            <table class="table table-striped" id="dt_table">
                                                <thead>
                                                <tr>
                                                    <th style="max-width: 55px;height: 35px;padding: 0 20px!important">@lang('image')</th>
                                                    <th style="height: 35px;padding: 0 20px!important">@lang('name')</th>
                                                    <th style="height: 35px;padding: 0 20px!important">@lang('sale_price')</th>
                                                    <th style="height: 35px;padding: 0 20px!important">@lang('count')</th>
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
                                                        <td>{{number_format($product->sale_price, 0 , ",", ".")}}</td>
                                                        <td width="400">
                                                            <div class="d-flex align-items-center">
                                                                <div class="quantity d-flex">
                                                                    <button class="minus-btn" type="button" name="button">-</button>
                                                                    <input type="text" class="order_count" name="order_count_{{$product->id}}" data-value="{{$product->id}}" data-price="{{$product->sale_price}}" value="0" min="0">
                                                                    <button class="plus-btn" type="button" name="button">+</button>
                                                                </div>
                                                                @if(count($agg_products) > 0)
                                                                    <button type="button" class="btn btn-black btn-sm btn-round ml-3" data-toggle="collapse" data-target="#collapse_{{$product->id}}" aria-expanded="false" aria-controls="collapse">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
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
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="comment">@lang('comment')</label>
                                            <textarea class="form-control" id="comment" name="comment"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex align-items-center justify-content-between mt-2 pr-4 pb-4">
                                        <h2 class="mb-0 ml-3">@lang('total'): <span id="new-total">0</span></h2>
                                        <button type="button" class="btn btn-black btn-round btn-order">@lang('add')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer  justify-content-end pt-1 pb-2">
                    <span id="sprint_active" class="mr-auto text-success px-4 py-2  d-none" data-toggle="tooltip" data-placement="right" title="Impresora activa"><i class="fas fa-print fa-2x"></i> </span>
                    <span id="sprint_inactive" class="mr-auto text-warning px-4 py-2" data-toggle="tooltip" data-placement="right" title="Impresora no disponible"><i class="fas fa-print fa-2x"></i> </span>

                    <button type="button" class="btn btn-black btn-round btn-print">@lang('print')</button>
                    <button type="button" class="btn btn-danger btn-round btn-pend">@lang('close_table')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <!-- JSP Print manager JS -->
    <script src="{{ asset('assets/js/print_tool_v2/zip.js/zip-full.min.js') }}"></script>
    <script src="{{ asset('assets/js/print_tool_v2/JSPrintManager.js') }}"></script>
    <script src="{{ asset('assets/js/print_tool_v2/bluebird.min.js') }}"></script>
    <script src="{{ asset('assets/js/utils_print.js') }}"></script>

    <!-- Datatable JS -->
    <script src="{{asset('assets/js/plugin/datatables/datatables.min.js')}}"></script>

    <script>

        let path_get_orders = '{{route('get-order-data')}}'
        let path_assign_orders = '{{route('assign-orders')}}'
        let path_table_list = '{{route('waiter.get-table-list')}}'
        let path_table_info = '{{route('get-table-info')}}'
        let path_create_orders = '{{route('create-order')}}'
        let path_pend_table = '{{route('waiter.pend-table')}}'
        let path_mark_deliver = '{{route('deliver-table-orders')}}'
        let path_delete_order = '{{route('delete-order')}}'
        let _token = '{{csrf_token()}}'
        let HOST_URL = "{{ url('/') }}"
    </script>
    <script src="{{asset('custom/js/waiter/table-list.js')}}?v=202204011155"></script>
@endsection
