@extends('resAdmin.res-layout.res-base')

@section('page-css')

@endsection

@section('content')
    <div class="container">
        <div class="panel-header bg-primary-gradient">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h1 class="text-white pb-2 fw-bold">{{$restaurant->name." / "}}<span style="font-size: 1.2rem">@lang('menu_qr')</span></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            <div class="row mt--2">
                <div class="col-sm-8 offset-sm-2 col-lg-6 offset-lg-3">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 d-flex align-items-center justify-content-center">
                                    <div id="qrcode"></div>
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
    <!-- Bootstrap Notify -->
    <script src="{{asset('assets/js/plugin/qrcode/qrcode.js')}}"></script>
    <script>
        var menuLink = '{{route('restaurant-menu',$code)}}';
    </script>
    <script src="{{asset('custom/js/resAdmin/generate-qrcode.js')}}"></script>
@endsection
