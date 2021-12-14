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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('information')</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt_table" class="display table table-striped table-hover" >
                                    <thead>
                                    <tr>
                                        <th>@lang('ID')</th>
                                        <th>@lang('name')</th>
                                        <th>@lang('creation_date')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($restaurants as $item)
                                        <tr>
                                            <td>{{$item->id}}</td>
                                            <td>
                                                <a href="{{ route('restaurant.detail',$item->id) }}">{{$item->name}}</a>
                                            </td>
                                            <td>{{$item->created_at}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
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
    <script src="{{asset('custom/js/resAdmin/restaurant-list.js')}}?v=202112061555"></script>
@endsection
