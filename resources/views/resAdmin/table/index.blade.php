@extends('resAdmin.res-layout.res-base')
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
                        <a href="#">@lang('tables')</a>
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
                                <a href="{{ route('restaurant.tables.create') }}" class="btn btn-black btn-round">@lang('create')</a>
                                <a href="{{ route('restaurant.tables.create-delivery') }}" class="btn btn-black btn-round">@lang('create_delivery')</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session()->has('payment_error'))
                                <div class="custom-error w-100">
                                    <h5 class="text-center">@lang('payment_error') @lang('try_again')</h5>
                                </div>
                            @endif
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
    <!-- Detail modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg-no" role="document" style="min-width:100%;position: absolute;bottom: 0;top: 0;left: 0;right: 0;margin:0;">
            <div class="modal-content" style="position: absolute;bottom: 0;top: 0;">
                <div class="modal-header pt-1 pb-0">
                    <h1 class="modal-title" id="detailModalLabel">@lang('detail')</h1>
                    <button type="button" class="close mt-1 pt-1" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0 pb-0" id="detail">
                    <div class="row">
                        <div class="col-12 col-md-4 pr-0">
                            <div class="view-order-list bg-light px-0 pt-0 pb-0">
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
                        <div class="col-12 col-md-8 pr-0">
                            <div class="bg-grey2 px-2 py-0 custom-scroll" style="overflow-y: scroll;height: calc(100vh - 130px);overflow-x: hidden;">
                                <div class="bg-black text-white px-3 py-1 mb-0">
                                    <h3 class="mb-0 text-center">@lang('add_order')</h3>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="dt_table">
                                                <thead>
                                                <tr>
                                                    <th style="height: 35px;padding: 0 20px!important">@lang('image')</th>
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
                                                        <td>
                                                            <div class="quantity d-flex">
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
                <div class="modal-footer justify-content-end pt-1 pb-2">
                    <span id="sprint_active" class="mr-auto text-success px-4 py-2  d-none" data-toggle="tooltip" data-placement="right" title="Impresora activa"><i class="fas fa-print fa-2x"></i> </span>
                    <span id="sprint_inactive" class="mr-auto text-warning px-4 py-2" data-toggle="tooltip" data-placement="right" title="Impresora no disponible"><i class="fas fa-print fa-2x"></i> </span>
                    <button type="button" class="btn btn-black btn-round btn-print">@lang('print')</button>
                    <button type="button" class="btn btn-black btn-round btn-confirm">@lang('confirm_payment')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Detail modal -->

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="confirmForm" action="{{ route('restaurant.close-table') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tableId" id="tableId" value="0">
                    <div class="modal-header bg-black">
                        <h1 class="modal-title" id="detailModalLabel">@lang('confirm_payment')</h1>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group form-show-validation pb-0">
                                    <label for="consumption">@lang('consumption')<span class="required-label">*</span></label>
                                    <input type="number" class="form-control font-weight-bold" id="consumption" name="consumption" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group form-show-validation pb-0">
                                    <label for="tip">@lang('tip')</label>
                                    <input type="number" class="form-control font-weight-bold" id="tip" name="tip">
                                </div>
                            </div>
                            <div class="col-6 px-0">
                                <div class="form-group form-show-validation pb-0 pl-0">
                                    <label for="shipping">@lang('shipping')</label>
                                    <input type="number" class="form-control" id="shipping" name="shipping">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group form-show-validation">
                                    <label for="payment_method">@lang('payment_method')<span class="required-label">*</span></label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="" selected disabled>@lang('select')</option>
                                        <option value="1">@lang('cash')</option>
                                        <option value="2">@lang('credit_or_debit')</option>
                                        <option value="3">@lang('transfer')</option>
                                        <option value="4">@lang('other')(@lang('specify'))</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 px-0 pt-2">
                                <div class="form-dgroup form-show-validation">
                                    <label class="d-block font-weight-bold" for="document_type">@lang('document_type')<span class="required-label">*</span></label>
                                    <div class="form-check form-check-inline pt-0">
                                      <input class="form-check-input" type="radio" name="document_type" id="document_type2" value="2">
                                      <label class="form-check-label pt-2" for="document_type2">@lang('electronic_ballot')</label>
                                    </div>
                                    <div class="form-check form-check-inline pt-0">
                                      <input class="form-check-input" type="radio" name="document_type" id="document_type4" value="4" checked>
                                      <label class="form-check-label pt-2" for="document_type4">@lang('receipt')</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-black btn-round btn-confirm-close">@lang('confirm_and_close_table')</button>
                        <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="reportModalLabel">@lang('report')</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detail">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <h2 class="font-weight-bold text-center">Boleta Electronica<br>N:No.Valido</h2>
                            <h6>Fecha: <span id="current_time">{{date('d/m/Y H:i')}}</span></h6>
                            <h6>Rut: <span id="rut-name"></span></h6>
                        </div>
                        <div class="col-12 col-md-4 offset-md-4">
                            <p class="font-weight-bold detail text-center">D E T A L L E</p>
                        </div>
                        <div class="col-12">
                            <div id="detail-list" class="b-b-grey">

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="b-b-grey py-2">
                                <h5 class="font-weight-bold text-right">Service : $<span id="service"></span></h5>
                                <h5 class="font-weight-bold text-right">TOTAL : $<span id="total"></span></h5>
                            </div>
                            <div class="b-b-grey py-2">
                                <h5 class="font-weight-bold">Tipo de page : <span id="tipo">EFECTIVO</span></h5>
                                <h5 class="font-weight-bold">Pago : <span>0</span></h5>
                                <h5 class="font-weight-bold">Vuelto : <span>0</span></h5>
                            </div>
                            <div class="mt-2">
                                <h5 class="text-center font-weight-bold">www.controlcash.cl</h5>
                                <h5 class="text-center">Resolucion Sll 80 de 2014</h5>
                                <h5 class="text-center font-weight-bold">Gracias por su compra</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
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
    <script src="{{asset('assets/js/plugin/moment/moment.min.js')}}"></script>

    <script>
        let tip_percentage = '10'
        let path_delete = '{{route('restaurant.tables.delete')}}'
        let path_table_list = '{{route('restaurant.tables.get-list')}}'
        let path_table_info = '{{route('get-table-info')}}'
        let path_close_table = '{{route('restaurant.close-table')}}'
        let path_delete_order = '{{route('delete-order')}}'
        let path_mark_deliver = '{{route('deliver-table-orders')}}'
        let path_create_orders = '{{route('create-order')}}'
        let _token = '{{csrf_token()}}'
        let HOST_URL = "{{ url('/') }}"
    </script>
    <script src="{{asset('custom/js/resAdmin/table-list.js')}}?v=202203170010"></script>
@endsection
