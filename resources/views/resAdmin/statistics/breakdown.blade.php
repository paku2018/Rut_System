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
    <script>
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
    </script>
@endsection
