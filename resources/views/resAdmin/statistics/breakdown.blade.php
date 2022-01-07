@extends('resAdmin.res-layout.res-base')

@section('page-css')

@endsection

@section('content')
    <div class="container">
        <div class="panel-header bg-primary-gradient">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">{{$restaurant->name}} @lang('breakdown')</h2>
                        <h5 class="text-white op-7 mb-2">@lang('breakdown_table_sale')</h5>
                    </div>
                    <div class="ml-md-auto py-2 py-md-0">
                        <div class="d-flex">
                            <div class="form-group">
                                <label class="text-white">@lang('start_date')</label>
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
                                <label class="text-white">@lang('end_date')</label>
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
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            <div class="row mt--2">
                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title">@lang('summary')</div>
                            <div class="card-category">@lang('information_restaurant')</div>
                            <div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-1"></div>
                                    <h6 class="fw-bold mt-3 mb-0">@lang('waiters')</h6>
                                </div>
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-2"></div>
                                    <h6 class="fw-bold mt-3 mb-0">@lang('tables')</h6>
                                </div>
                                <div class="px-2 pb-2 pb-md-0 text-center">
                                    <div id="circles-3"></div>
                                    <h6 class="fw-bold mt-3 mb-0">@lang('products')</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card full-height">
                        <div class="card-body">
                            <div class="card-title">@lang('sale_statistics')</div>
                            <div class="row py-3">
                                <div class="col-md-4 d-flex flex-column justify-content-around">
                                    <div>
                                        <h6 class="fw-bold text-uppercase text-success op-8">@lang('total_sale')</h6>
                                        <h3 class="fw-bold">${{number_format($sales, 2)}}</h3>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-uppercase text-info op-8">@lang('total_tip')</h6>
                                        <h3 class="fw-bold">${{number_format($tips, 2)}}</h3>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div id="chart-container">
                                        <canvas id="totalSaleChart"></canvas>
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
    <script src="{{asset('assets/js/plugin/chart.js/chart.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/chart-circle/circles.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/moment/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/plugin/datepicker/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        var link = '{{ route('restaurant.statistics.breakdown') }}'
        var start_date = '{{ date('m/d/Y', strtotime($start_date)) }}'
        var end_date = '{{ date('m/d/Y', strtotime($end_date)) }}'

        let waiters = '{{$waiters}}'
        let tables = '{{$tables}}'
        let products = '{{$products}}'
        let sales = parseFloat('{{$sales}}')
        let tips = parseFloat('{{$tips}}')
        let shipping = parseFloat('{{$shipping}}')

        Circles.create({
            id:'circles-1',
            radius:45,
            value:60,
            maxValue:100,
            width:7,
            text: waiters,
            colors:['#f1f1f1', '#FF9E27'],
            duration:400,
            wrpClass:'circles-wrp',
            textClass:'circles-text',
            styleWrapper:true,
            styleText:true
        })

        Circles.create({
            id:'circles-2',
            radius:45,
            value:70,
            maxValue:100,
            width:7,
            text: tables,
            colors:['#f1f1f1', '#2BB930'],
            duration:400,
            wrpClass:'circles-wrp',
            textClass:'circles-text',
            styleWrapper:true,
            styleText:true
        })

        Circles.create({
            id:'circles-3',
            radius:45,
            value:40,
            maxValue:100,
            width:7,
            text: products,
            colors:['#f1f1f1', '#1249ee'],
            duration:400,
            wrpClass:'circles-wrp',
            textClass:'circles-text',
            styleWrapper:true,
            styleText:true
        })

        var totalSaleChart = document.getElementById('totalSaleChart').getContext('2d');

        var mytotalIncomeChart = new Chart(totalSaleChart, {
            type: 'bar',
            data: {
                labels: ["{{__('total_sale')}}", "{{__('total_tip')}}", "{{__('total_shipping')}}"],
                datasets : [{
                    label: "{{__('amount')}}",
                    backgroundColor: '#ff9e27',
                    borderColor: 'rgb(23, 125, 255)',
                    data: [sales, tips, shipping],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false,
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            display: false
                        },
                        gridLines : {
                            drawBorder: false,
                            display : false
                        }
                    }],
                    xAxes : [ {
                        gridLines : {
                            drawBorder: false,
                            display : false
                        }
                    }]
                },
            }
        });

        $('.datepicker').datetimepicker({
            format: 'DD/MM/YYYY',
        });

        $(document).ready(function () {
            $('[name="end_date"]').val(end_date);
            $('[name="start_date"]').val(start_date);
        })

        $('#search').on('click', function () {
            let from_date = $('#start_date').val();
            let arr = from_date.split("/")
            let start = arr[2] + "-" + arr[1] + "-" + arr[0]
            let to_date = $('#end_date').val();
            let spl = to_date.split("/")
            let end = spl[2] + "-" + spl[1] + "-" + spl[0]

            location.href = link + "?start=" + start + "&&end=" + end;
        })
    </script>
@endsection
