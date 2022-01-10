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
                        <a href="#">@lang('statistics')</a>
                    </li>
                    <li class="separator">
                        <i class="flaticon-right-arrow"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('restaurant.statistics.best-products') }}">@lang('best_selling_product')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('best_selling_product')</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex">
                                    <div class="form-group">
                                        <label>@lang('start_date')</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control datepicker" id="start_date" name="start_date">
                                            <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar-check"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('end_date')</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control datepicker" id="end_date" name="end_date">
                                            <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar-check"></i>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">@lang('category')</label>
                                        <select class="form-control" id="category" name="category" style="width: 200px">
                                            <option value="">@lang('select')</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group d-flex align-items-center">
                                        <button class="btn btn-black btn-round" id="search">@lang('search')</button>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-black btn-round" id="export">Excel</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="dt_table" class="display table table-striped table-hover" >
                                    <thead>
                                    <tr>
                                        <th>@lang('product')</th>
                                        <th>@lang('amount')</th>
                                        <th>@lang('purchase_price') @lang('total')</th>
                                        <th>@lang('total')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
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
        var path_data = '{{ route('restaurant.statistics.best-products.get-data') }}'
        var path_export = '{{ route('restaurant.statistics.best-products.export') }}'
    </script>
    <script src="{{asset('custom/js/resAdmin/statistics/best-products.js')}}?v=202201071555"></script>
@endsection
