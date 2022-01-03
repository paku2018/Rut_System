<!-- Sidebar -->
@php
    $main = Request::segment(1);
    $link = Request::segment(2);
    $sublink = Request::segment(3);
    $user = \Illuminate\Support\Facades\Auth::user();
    $resId = session()->get('resId');
@endphp
<div class="sidebar sidebar-style-2" data-background-color="dark2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    <img src="{{isset($user->avatar)?$user->avatar:asset('assets/img/profile.jpg')}}" onerror="src='{{asset('assets/img/profile.jpg')}}'" alt="no_img" class="avatar-img rounded-circle">
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="javascript:;">
                        <span>
                            {{$user->name}}
                            <span class="user-level">{{__('restaurant_admin')}}</span>
                        </span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <ul class="nav nav-primary">
                <li class="nav-item {{($main=="dashboard" || $link=="home")?'active':''}}">
                    <a href="{{route('home')}}">
                        <i class="fas fa-home"></i>
                        <p>@lang('dashboard')</p>
                    </a>
                </li>
                <li class="nav-item {{$link=="members"?'active':''}}">
                    <a href="{{ route('restaurant.members.list') }}">
                        <i class="fas fa-user-friends"></i>
                        <p>@lang('members')</p>
                    </a>
                </li>
                <li class="nav-item {{$link=="tables"?'active':''}}">
                    <a href="{{ route('restaurant.tables.list') }}">
                        <i class="fas fa-table"></i>
                        <p>@lang('tables')</p>
                    </a>
                </li>
                <li class="nav-item {{$link=="categories"?'active':''}}">
                    <a href="{{ route('restaurant.categories.list') }}">
                        <i class="fas fa-align-left"></i>
                        <p>@lang('categories')</p>
                    </a>
                </li>
                <li class="nav-item {{$link=="products"?'active':''}}">
                    <a href="{{ route('restaurant.products.list') }}">
                        <i class="fas fa-cubes"></i>
                        <p>@lang('products')</p>
                    </a>
                </li>
                <li class="nav-item {{$link=="qrcode"?'active':''}}">
                    <a href="{{ route('restaurant.qrcode') }}">
                        <i class="fas fa-qrcode"></i>
                        <p>@lang('menu_qr')</p>
                    </a>
                </li>
                <li class="nav-item {{$link=="statistics"?'active':''}}">
                    <a data-toggle="collapse" href="#statistics">
                        <i class="fas fa-chart-area"></i>
                        <p>@lang('statistics')</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{$link=="statistics"?'show':''}}" id="statistics">
                        <ul class="nav nav-collapse">
                            <li class="{{$link=="statistics"&&$sublink=="sales"?'active':''}}">
                                <a href="{{ route('restaurant.statistics.sales') }}">
                                    <span class="sub-item">@lang('sales')</span>
                                </a>
                            </li>
                            <li class="{{$link=="statistics"&&$sublink=="home_sales"?'active':''}}">
                                <a href="#">
                                    <span class="sub-item">@lang('home_sales')</span>
                                </a>
                            </li>
                            <li class="{{$link=="statistics"&&$sublink=="best_selling_product"?'active':''}}">
                                <a href="#">
                                    <span class="sub-item">@lang('best_selling_product')</span>
                                </a>
                            </li>
                            <li class="{{$link=="statistics"&&$sublink=="breakdown"?'active':''}}">
                                <a href="#">
                                    <span class="sub-item">@lang('breakdown')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div class="text-center mt-3">
                <a href="{{ route('restaurant.list') }}" class="text-white"><i class="fas fa-arrow-left mr-2"></i>@lang('go_back_list')</a>
            </div>
        </div>
    </div>
</div>
<!-- End Sidebar -->
