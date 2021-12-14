<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ env('APP_NAME') }}</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

    <!-- Fonts and icons -->
    <script src="{{asset('assets/js/plugin/webfont/webfont.min.js')}}"></script>
    <script>
        WebFont.load({
            google: {"families":["Lato:300,400,700,900"]},
            custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['../assets/css/fonts.min.css']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- Global stylesheets -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/atlantis.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/css/spinners.css')}}">

    <script src="{{ asset('custom/lang/converter.js') }}?v=202112061555"></script>
    <script src="{{ asset('custom/lang/es.js') }}?v=202112061555"></script>
    <script src="{{ asset('assets/js/spinner.js') }}"></script>
</head>
<body class="login">
<div class="preloader" style="display: none">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
    </svg>
</div>
<div class="wrapper wrapper-login">
    <div class="container container-login animated fadeIn">
        <h3 class="text-center">@lang('verify_email')</h3>
        <div class="login-form">
            <div class="form-group form-floating-label">
                <input id="code" name="code" type="text" class="form-control input-border-bottom" required>
                <label for="code" class="placeholder">@lang('code')</label>
            </div>
            <div class="code-error ml-2 d-none">
                <span class="error-text" style="color: red">@lang('code_incorrect')</span>
            </div>
            <div class="form-action mb-3">
                <a href="javascript:void(0);" class="btn btn-black btn-round" onclick="checkCode()">@lang('confirm')</a>
            </div>
            <div class="login-account">
                <span class="msg">@lang('not_have_code')</span>
                <a href="javascript:void(0);" id="resend_code" class="link" onclick="resendCode()">@lang('resend_code')</a>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
<script src="{{asset('assets/js/plugin/jquery.validate/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/plugin/sweetalert/sweetalert.min.js')}}"></script>
<script>
    let _token = '{{csrf_token()}}';
    let path_verify = '{{route('check-verify-code')}}'
    let path_home = '{{route('home')}}'
    let path_resend = '{{route('resend-code')}}'
</script>
<script src="{{asset('custom/js/verify.js')}}"></script>
</body>
</html>
