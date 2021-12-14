<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{env('APP_NAME')}}</title>

    <!-- Global stylesheets -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/atlantis.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/fonts.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/css/spinners.css')}}">

    <!-- custom stylesheets -->
    <link href="{{ asset('custom/css/style.css') }}" rel="stylesheet">

    <!-- /global stylesheets -->
    @yield('page-css')
    <!-- Core JS files -->
    <script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <!-- /core JS files -->

    <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{asset('assets/js/plugin/jquery.validate/jquery.validate.min.js')}}"></script>
    <script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
    <script src="{{asset('assets/js/plugin/sweetalert/sweetalert.min.js')}}"></script>
    <script src="{{ asset('assets/js/spinner.js') }}"></script>

    <script src="{{ asset('custom/lang/converter.js') }}?v=202112061555"></script>
    <script src="{{ asset('custom/lang/es.js') }}?v=202112061555"></script>
</head>
<body>
<div class="preloader" style="display: none">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
    </svg>
</div>
<div class="wrapper">

<!-- Main navbar -->
@include('layouts.navbar')
<!-- /main navbar -->

    <!-- Page content -->
    <div class="page-content">
        <!-- Sidebar area -->
        @include('resAdmin.res-layout.res-sidebar')
        <!-- /Sidebar area -->

        <!-- Main content -->
        <div class="main-panel">

        <!-- Content area -->
        @yield('content')
        <!-- /content area -->

        </div>
        <!-- /main content -->
    </div>
</div>
<!-- /page content -->
@yield('page-js')

</body>

</html>
