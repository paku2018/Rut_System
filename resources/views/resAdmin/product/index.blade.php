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
                        <a href="{{ route('restaurant.products.list') }}">@lang('products')</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="card-title">@lang('product_list')</h4>
                            <a href="{{ route('restaurant.products.create') }}" class="btn btn-black btn-round">@lang('create')</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt_table" class="display table table-striped table-hover" >
                                    <thead>
                                    <tr>
                                        <th>@lang('ID')</th>
                                        <th>@lang('name')</th>
                                        <th>@lang('category')</th>
                                        <th>@lang('purchase_price')</th>
                                        <th>@lang('sale_price')</th>
                                        <th>@lang('description')</th>
                                        <th>@lang('availability')</th>
                                        <th class="text-center">@lang('action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($products as $one)
                                        <tr>
                                            <td>{{$one->id}}</td>
                                            <td>{{$one->name}}</td>
                                            <td>{{$one->category?$one->category->name:''}}</td>
                                            <td>{{$one->purchase_price}}</td>
                                            <td>{{$one->sale_price}}</td>
                                            <td>{{$one->desc}}</td>
                                            <td>
                                                <input type="checkbox" class="status" id="status_{{$one->id}}" data-index="{{$one->id}}" data-toggle="toggle" data-onstyle="primary" data-style="btn-round" {{$one->status==1?'checked':''}}>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-button-action">
                                                    <a href="{{ route('restaurant.products.edit', $one->id) }}" type="button" class="btn btn-link btn-primary btn-lg">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" onclick="delProduct({{$one->id}})" class="btn btn-link btn-danger">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
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
    <script src="{{asset('assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js')}}"></script>
    <script>
        var path_delete = '{{ route('restaurant.products.delete') }}';
        var path_change = '{{route('restaurant.products.change')}}'
        var _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/resAdmin/product-list.js')}}?v=202112061555"></script>
@endsection
