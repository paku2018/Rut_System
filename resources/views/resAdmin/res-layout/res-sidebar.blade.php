<!-- Sidebar -->
@php
    $main = Request::segment(1);
    $link = Request::segment(2);
    $sublink = Request::segment(3);
    $user = \Illuminate\Support\Facades\Auth::user();
    $resId = session()->get('resId');
    $access_role = $user->role;
    $has_permissions = getPermissions();
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
                @if($access_role == "restaurant")
                    <li class="nav-item {{($main=="dashboard" || $link=="home")?'active':''}}">
                        <a href="{{route('home')}}">
                            <i class="fas fa-utensils"></i>
                            <p>@lang('restaurants')</p>
                        </a>
                    </li>
                    <li class="nav-item {{$link=="members"?'active':''}}">
                        <a href="{{ route('restaurant.members.list') }}">
                            <i class="fas fa-user-friends"></i>
                            <p>@lang('members')</p>
                        </a>
                    </li>
                    <li class="nav-item {{$link=="permission"?'active':''}}">
                        <a href="{{ route('restaurant.permission.index') }}">
                            <i class="fas fa-shield-alt"></i>
                            <p>@lang('permission')</p>
                        </a>
                    </li>
                @endif
                @if($access_role == "restaurant" || in_array("tables.view", $has_permissions))
                    <li class="nav-item {{$link=="tables"?'active':''}}">
                        <a href="{{ route('restaurant.tables.list') }}">
                            <i class="fas fa-table"></i>
                            <p>@lang('tables')</p>
                        </a>
                    </li>
                @endif
                @if($access_role == "restaurant" || in_array("categories.view", $has_permissions))
                    <li class="nav-item {{$link=="categories"?'active':''}}">
                        <a href="{{ route('restaurant.categories.list') }}">
                            <i class="fas fa-align-left"></i>
                            <p>@lang('categories')</p>
                        </a>
                    </li>
                @endif
                @if($access_role == "restaurant" || in_array("products.view", $has_permissions))
                    <li class="nav-item {{$link=="products"?'active':''}}">
                        <a href="{{ route('restaurant.products.list') }}">
                            <i class="fas fa-cubes"></i>
                            <p>@lang('products')</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item {{$link=="qrcode"?'active':''}}">
                    <a href="{{ route('restaurant.qrcode') }}">
                        <i class="fas fa-qrcode"></i>
                        <p>@lang('menu_qr')</p>
                    </a>
                </li>
                @if($access_role == "restaurant" || in_array("sales.view", $has_permissions))
                    <li class="nav-item {{$link=="sales"?'active':''}}">
                        <a href="{{ route('restaurant.sales.index') }}">
                            <i class="fas fa-credit-card"></i>
                            <p>@lang('sales_panel')</p>
                        </a>
                    </li>
                @endif
                @if($access_role == "restaurant" || in_array("statistics.view", $has_permissions))
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
                                <li class="{{$link=="statistics"&&$sublink=="orders"?'active':''}}">
                                    <a href="{{ route('restaurant.statistics.orders') }}">
                                        <span class="sub-item">@lang('orders')</span>
                                    </a>
                                </li>
                                <li class="{{$link=="statistics"&&$sublink=="bestProducts"?'active':''}}">
                                    <a href="{{ route('restaurant.statistics.best-products') }}">
                                        <span class="sub-item">@lang('best_selling_product')</span>
                                    </a>
                                </li>
                                <li class="{{$link=="statistics"&&$sublink=="breakdown"?'active':''}}">
                                    <a href="{{ route('restaurant.statistics.breakdown') }}">
                                        <span class="sub-item">@lang('breakdown')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
            @if($access_role == "restaurant")
                <div class="text-center mt-3">
                    <a href="{{ route('restaurant.list') }}" class="text-white"><i class="fas fa-arrow-left mr-2"></i>@lang('go_back_list')</a>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- End Sidebar -->
