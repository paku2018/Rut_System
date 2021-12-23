@extends('resAdmin.res-layout.res-base')
@section('page-css')
    <style>
        .table-box{
            cursor: pointer;
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
                            <a href="{{ route('restaurant.tables.create') }}" class="btn btn-black btn-round">@lang('create')</a>
                        </div>
                        <div class="card-body">
                            @if(session()->has('payment_error'))
                                <div class="custom-error w-100">
                                    <h5 class="text-center">@lang('payment_error') @lang('try_again')</h5>
                                </div>
                            @endif
                            @if(count($tables) > 0)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-group">
                                            @foreach($tables as $table)
                                                @php
                                                    switch ($table->status){
                                                        case "open":
                                                            $class = "bg-success-gradient success-shadow";
                                                            $title = __("open");
                                                            break;
                                                        case "ordered":
                                                            $class = "bg-warning-gradient";
                                                            $title = __("ordered");
                                                            break;
                                                        case "pend":
                                                            $class = "bg-danger-gradient";
                                                            $title = __("provisional_close");
                                                            break;
                                                        case "closed":
                                                            $class = "bg-black";
                                                            $title = __("available");
                                                            break;
                                                        default:
                                                            $class = '';
                                                            $title = '';
                                                            break;
                                                    }
                                                @endphp
                                                <div class="table-box" data-index="{{$table->id}}">
                                                    <div class="table-status {{$class}}" title="{{$title}}"></div>
                                                    <h6 class="text-center mb-0">@lang('table')-{{$table->t_number}}</h6>
                                                    <h5 class="text-center">{{$table->name}}</h5>
                                                    <div class="table-action d-flex align-items-center justify-content-center">
                                                        <a href="{{route('restaurant.tables.edit', $table->id)}}" class="text-black"><i class="fas fa-edit"></i></a>
                                                        <div class="ml-2 text-red delete" data-index="{{$table->id}}"><i class="fas fa-trash"></i></div>
                                                    </div>
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
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" id="detailModalLabel">@lang('detail')</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="detail">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="view-order-list bg-light px-2 py-3">
                                <div class="bg-black text-white px-3 py-1 mb-3">
                                    <h3 class="mb-0 text-center">@lang('order_list')</h3>
                                </div>
                                <div id="assigned-orders">

                                </div>
                                <div class="mt-3">
                                    <h1 class="text-right">@lang('total') : <span id="detail-total"></span></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-black btn-round btn-confirm">@lang('confirm_payment')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="confirmForm" action="{{ route('restaurant.close-table') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tableId" id="tableId" value="0">
                    <div class="modal-header">
                        <h1 class="modal-title" id="detailModalLabel">@lang('confirm_payment')</h1>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group form-show-validation">
                                    <label for="consumption">@lang('consumption')<span class="required-label">*</span></label>
                                    <input type="text" class="form-control" id="consumption" name="consumption" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group form-show-validation">
                                    <label for="tip">@lang('tip')</label>
                                    <input type="text" class="form-control" id="tip" name="tip">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group form-show-validation">
                                    <label for="shipping">@lang('shipping')</label>
                                    <input type="text" class="form-control" id="shipping" name="shipping">
                                </div>
                            </div>
                            <div class="col-12">
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
                            <div class="col-12">
                                <div class="form-group form-show-validation">
                                    <label for="document_type">@lang('document_type')<span class="required-label">*</span></label>
                                    <select class="form-control" id="document_type" name="document_type">
                                        <option value="1" selected>@lang('final_close')</option>
                                        <option value="2">@lang('electronic_ballot')</option>
                                        <option value="3">@lang('invoice')</option>
                                        <option value="4">@lang('receipt')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-black btn-round">@lang('confirm_and_close_table')</button>
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
    <!-- Datatable JS -->
    <script src="{{asset('assets/js/plugin/moment/moment.min.js')}}"></script>
    <script>
        let path_delete = '{{route('restaurant.tables.delete')}}'
        let path_table_info = '{{route('get-table-info')}}'
        let path_close_table = '{{route('restaurant.close-table')}}'
        let _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/resAdmin/table-list.js')}}?v=202112221555"></script>
@endsection
