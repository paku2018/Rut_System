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
                            @if(count($tables) > 0)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-group">
                                            @foreach($tables as $table)
                                                <div class="table-box">
                                                    <div class="table-status {{$table->status=="open"?'bg-success-gradient success-shadow':'bg-warning-gradient'}}" title="{{$table->status=="open"?__('open'):__('closed')}}"></div>
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
@endsection
@section('page-js')
    <!-- Datatable JS -->
    <script>
        let path_delete = '{{route('restaurant.tables.delete')}}'
        let _token = '{{csrf_token()}}'
    </script>
    <script src="{{asset('custom/js/resAdmin/table-list.js')}}"></script>
@endsection
