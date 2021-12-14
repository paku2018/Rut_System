<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ env('APP_NAME') }}:500</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

    <!-- CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/atlantis.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/fonts.min.css') }}" rel="stylesheet">

</head>
<body class="page-not-found">
<div class="wrapper not-found">
    <h1 class="animated fadeIn">500</h1>
    <div class="desc animated fadeIn"><span>@lang('oops')</span><br/>@lang('error_in_request')</div>
    <a href="{{route('home')}}" class="btn btn-primary btn-back-home mt-4 animated fadeInUp">
			<span class="btn-label mr-2">
				<i class="flaticon-home"></i>
			</span>
        @lang('home')
    </a>
</div>
<script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
