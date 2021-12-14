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
                    <nav class="col-md-6 col-12 tm-nav">
                        <ul class="tm-nav-ul">
                            <li class="tm-nav-li"><a href="#" class="tm-nav-link active">Home</a></li>
                            @if(\Illuminate\Support\Facades\Auth::check() == false)
                                <li class="tm-nav-li"><a href="{{route('login')}}" class="tm-nav-link">@lang('login')</a></li>
                                <li class="tm-nav-li"><a href="{{route('register')}}" class="tm-nav-link">@lang('register')</a></li>
                            @endif
                        </ul>
                    </nav>
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
                    <button type="button" class="btn btn-order">@lang('order_now')</button>
                    <input type="hidden" id="logged-status" value="{{\Illuminate\Support\Facades\Auth::check()}}">
                </div>
                @if(\Illuminate\Support\Facades\Auth::check() == false)
                    <div class="alert mb-4 col-12">
                        <h5 class="text-danger text-center">Before ordering, please make sure to <a href="{{ route('login') }}">@lang('login')</a></h5>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <footer class="tm-footer text-center">
        <p>Copyright &copy; {{date('Y')}} {{env('APP_NAME')}}</p>
    </footer>
</div>
<script src="{{ asset('custom/front/js/jquery.min.js') }}?v=202112061555"></script>
<script src="{{ asset('custom/front/js/parallax.min.js') }}?v=202112061555"></script>
<script>
    var _token = '{{csrf_token()}}'
    var path_order = '{{route('order')}}'
</script>
<script src="{{ asset('custom/front/js/menu.js') }}?v=202112061555"></script>
</body>
</html>
