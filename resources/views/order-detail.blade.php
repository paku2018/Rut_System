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
                        <img src="{{asset('assets/img/logo.png')}}" alt="Logo" class="tm-site-logo" style="width: 100px"/>
                        <div class="tm-site-text-box">
                            <h1 class="tm-site-title">{{$table['restaurant']['name']}}</h1>
                            <h6 class="tm-site-description">Por favor verifique los detalles del pedido</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main>
        <header class="row tm-welcome-section">
            <h2 class="col-12 text-center tm-section-title">Bienvenidos a {{$table->restaurant->name}}</h2>
            <p class="col-12 text-center">Le damos la bienvenida y disfrute de una excelente experiencia gastronómica.</p>
        </header>

        <div class="menu-list">
            <div class="row">
                <div class="col-md-12">
                    <div class="bg-light px-2 py-0 mx-5">
                        <div class="bg-black text-white px-3 py-1 mb-3">
                            <h3 class="mb-0 text-center">@lang('order_list')</h3>
                        </div>
                        <div id="assigned-orders">
                            @foreach($orders as $item)
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h4 class="text-danger mb-0">{{$item->product->name}}</h4>
                                    </div>
                                    <h4 class="text-right mb-1">{{$item->product->sale_price}}*{{$item->order_count}}={{$item->product->sale_price * $item->order_count}}</h4>
                                    <input class="order_val" type="hidden" value="{{$item->product->sale_price * $item->order_count}}">
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <h3 class="text-right">Subtotal : <span id="sub-total"></span></h3>
                            <h3 class="text-right">Tip : <span id="tip"></span></h3>
                            <h3 class="text-right">Total : <span id="total"></span></h3>
                        </div>
                        <div class="mt-3">
                            <p>{{$table->restaurant->bank_transfer_details}}</p>
                        </div>
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
    $(document).ready(function () {
        let total_val = 0;
        $('.order_val').each(function () {
            let val = parseInt($(this).val())
            total_val = total_val + val;
        })
        $('#sub-total').text(total_val);
        $('#tip').text(parseInt(total_val/10));
        $('#total').text(total_val + parseInt(total_val/10));
    })
</script>
<script src="{{ asset('custom/front/js/menu.js') }}?v=202112221555"></script>
</body>
</html>
