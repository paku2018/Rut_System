@extends('layouts.base')
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
                                                    <div class="table-status {{$table->status=="open"?'bg-success-gradient success-shadow':($table->status=="closed"?'bg-warning-gradient':'bg-danger-gradient')}}" title="{{$table->status=="open"?__('open'):($table->status=="closed"?__('closed'):__('provisional_close'))}}"></div>
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
                    <button type="button" class="btn btn-black btn-round btn-report">@lang('report')</button>
                    <button type="button" class="btn btn-danger btn-round btn-close">@lang('close_table')</button>
                    <button type="button" class="btn btn-round" data-dismiss="modal">@lang('cancel')</button>
                </div>
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
    <script src="{{asset('assets/js/plugin/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/moment/moment.min.js')}}"></script>
    <script>
        let path_table_info = '{{route('get-table-info')}}'
        let path_close_table = '{{route('cashier.close-table')}}'
        let _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/cashier/table-list.js')}}"></script>
@endsection
