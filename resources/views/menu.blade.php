<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{env('APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400" rel="stylesheet" />
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('custom/front/css/templatemo-style.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/css/spinners.css')}}">
    <!-- custom stylesheets -->
    <link href="{{ asset('custom/css/style.css') }}" rel="stylesheet">

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
<div class="container">
    <!-- Top box -->
    <!-- Logo & Site Name -->
    <div class="placeholder">
        <div class="parallax-window" data-parallax="scroll" data-image-src="{{asset('assets/img/auth.jpg')}}">
            <div class="tm-header">
                <div class="row tm-header-inner">
                    <div class="col-md-6 col-12">
                        <img src="{{asset('assets/img/logo.png')}}" alt="Logo" class="tm-site-logo" />
                        <div class="tm-site-text-box">
                            <h1 class="tm-site-title">{{$restaurant->name}}</h1>
                            <h6 class="tm-site-description">@lang('please_order_here')</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main>
        <header class="row tm-welcome-section">
            <h2 class="col-12 text-center tm-section-title">Welcome to {{$restaurant->name}}</h2>
            <p class="col-12 text-center">Dear guests, you are welcomed at our restaurant. We greatly appreciate your choice of dining with us and we promise to serve you with our excellence. Welcome you and have a fine dining experience.</p>
        </header>

        <!-- Gallery -->
        <div class="row tm-gallery">
            <div id="tm-gallery-page-pizza" class="tm-gallery-page">
                @foreach($products as $product)
                    <article class="col-lg-3 col-md-4 col-sm-6 col-12 tm-gallery-item">
                        <figure>
                            <img src="{{$product->image}}" alt="Image" class="img-fluid tm-gallery-img" />
                            <figcaption>
                                <h4 class="tm-gallery-title">{{$product->name}}</h4>
                                <p class="tm-gallery-description">{{$product->desc}}</p>
                                <div class="tm-gallery-bottom d-flex align-items-center justify-content-between">
                                    <p class="tm-gallery-price">${{number_format($product->sale_price,0)}}</p>
                                    <div class="quantity">
                                        <button class="minus-btn" type="button" name="button">-</button>
                                        <input type="text" class="order_count" name="order_count_{{$product->id}}" data-value="{{$product->id}}" value="0" min="0">
                                        <button class="plus-btn" type="button" name="button">+</button>
                                    </div>
                                </div>
                            </figcaption>
                        </figure>
                    </article>
                @endforeach
                <div class="order-box text-center col-12">
                    <button type="button" class="btn btn-order" id="order">@lang('order_now')</button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="verifyModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verifyModalLongTitle">@lang('order_info')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="email">@lang('email')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="email" name="email" required onchange="checkConfirmBtn()">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="v_code">@lang('verification_code')<span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="v_code" name="v_code" onchange="checkConfirmBtn()">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" id="send_code">@lang('send_code')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check pl-0">
                                <label class="form-radio-label">
                                    <input class="form-radio-input" type="radio" name="orderType" value="0" checked onchange="checkOrderType()">
                                    <span class="form-radio-sign">@lang('table')</span>
                                </label>
                                <label class="form-radio-label ml-3">
                                    <input class="form-radio-input" type="radio" name="orderType" value="1" onchange="checkOrderType()">
                                    <span class="form-radio-sign">@lang('delivery')</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 table-option">
                            <div class="form-group">
                                <label for="table">@lang('table')<span class="text-danger">*</span></label>
                                <select class="form-control" id="table" name="table" required onchange="checkConfirmBtn()">
                                    <option value="" disabled selected>@lang('select')</option>
                                    @foreach($tables as $table)
                                        <option value="{{$table->id}}">@lang('table')-{{$table->t_number}}({{$table->name}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-order" id="confirm-order">@lang('order_now')</button>
                        <button type="button" class="btn btn-round btn-secondary btn-circle" data-dismiss="modal">@lang('cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="tm-footer text-center">
        <p>Copyright &copy; {{date('Y')}} {{env('APP_NAME')}}</p>
    </footer>
</div>
<script src="{{ asset('custom/front/js/jquery.min.js') }}?v=202112061555"></script>
<script src="{{ asset('custom/front/js/parallax.min.js') }}?v=202112061555"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script>
    var _token = '{{csrf_token()}}'
    var path_order = '{{route('order')}}'
    var path_mail = '{{route('send-verification-mail')}}'
</script>
<script src="{{ asset('custom/front/js/menu.js') }}?v=202112061555"></script>
</body>
</html>
