@extends('layouts.base')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h4 class="page-title">@lang('restaurants')</h4>
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
                </ul>
            </div>
            <div class="row">
                @foreach($restaurants as $restaurant)
                    <div class="col-md-4">
                        <div class="card card-dark bg-secondary-gradient single-rest" data-index="{{$restaurant->id}}" style="cursor: pointer">
                            <div class="card-body bubble-shadow">
                                <h1>{{$restaurant->name}}</h1>
                                <h5 class="op-8">{{count($restaurant->tables)}} @lang('tables')</h5>
                                <div class="pull-right">
                                    <h3 class="fw-bold op-8">{{$restaurant->tax_id}}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script>
        var HOST_URL = '{{url('/')}}'
    </script>
    <script src="{{asset('custom/js/resAdmin/restaurant-list.js')}}?v=202204051555"></script>
@endsection
