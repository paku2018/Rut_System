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
                        <a href="{{ route('restaurant.statistics.orders') }}">@lang('orders')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('information')</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex mb-3">
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
                                <div class="form-group d-flex align-items-center">
                                    <button class="btn btn-black btn-round" id="search">@lang('search')</button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 col-md-6">
                                    <h2 class="text-center">@lang('order_counts')</h2>
                                    <div class="chart-container">
                                        <canvas id="barCountChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h2 class="text-center">@lang('order_totals')</h2>
                                    <div class="chart-container">
                                        <canvas id="barTotalChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script src="{{asset('assets/js/plugin/moment/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/chart.js/chart.min.js')}}"></script>
    <script>
        var homeCount = parseInt('{{$home_order_count}}')
        var deliveryCount = parseInt('{{$delivery_order_count}}')
        var homeTotal = parseFloat('{{$home_order_total}}')
        var deliveryTotal = parseFloat('{{$delivery_order_total}}')

        var barCountChart = document.getElementById('barCountChart').getContext('2d');
        var firstBarChart = new Chart(barCountChart, {
            type: 'bar',
            data: {
                labels: ["{{__('total')}}", "{{__('home_orders')}}", "{{__('delivery')}}"],
                datasets : [{
                    label: "{{__('count')}}",
                    backgroundColor: 'rgb(23, 125, 255)',
                    borderColor: 'rgb(23, 125, 255)',
                    data: [homeCount + deliveryCount, homeCount, deliveryCount],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                },
            }
        });

        var barTotalChart = document.getElementById('barTotalChart').getContext('2d');
        var secondBarChart = new Chart(barTotalChart, {
            type: 'bar',
            data: {
                labels: ["{{__('total')}}", "{{__('home_orders')}}", "{{__('delivery')}}"],
                datasets : [{
                    label: "{{__('price')}}" + "($)",
                    backgroundColor: 'rgb(38,255,23)',
                    borderColor: 'rgb(38,255,23)',
                    data: [homeTotal + deliveryTotal, homeTotal, deliveryTotal],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                },
            }
        });

        var link = '{{ route('restaurant.statistics.orders') }}'
        var start_date = '{{ date('m/d/Y', strtotime($start_date)) }}'
        var end_date = '{{ date('m/d/Y', strtotime($end_date)) }}'
    </script>
    <script src="{{asset('custom/js/resAdmin/statistics/orders.js')}}?v=202201061555"></script>
@endsection
