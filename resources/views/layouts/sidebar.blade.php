<!-- Sidebar -->
@php
    $main = Request::segment(1);
    $link = Request::segment(2);
    $sublink = Request::segment(3);
    $user = \Illuminate\Support\Facades\Auth::user();
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
                            <span class="user-level">{{$user->role=="admin"?__('admin'):($user->role=="restaurant"?__('restaurant_admin'):__('waiter'))}}</span>
                        </span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <ul class="nav nav-primary">
                @if($user->role == "admin" || $user->role == "restaurant")
                <li class="nav-item {{($main=="dashboard" || $link=="home")?'active':''}}">
                    <a href="{{route('home')}}">
                        <i class="fas fa-home"></i>
                        <p>@lang('dashboard')</p>
                    </a>
                </li>
                @endif
                @if($user->role == "admin")
                    <li class="nav-item {{$link=="user"?'active':''}}">
                        <a data-toggle="collapse" href="#user">
                            <i class="fas fa-users"></i>
                            <p>@lang('users')</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{$link=="user"?'show':''}}" id="user">
                            <ul class="nav nav-collapse">
                                <li class="{{$link=="user"&&$sublink=="list"?'active':''}}">
                                    <a href="{{ route('admin.user.list') }}">
                                        <span class="sub-item">@lang('list')</span>
                                    </a>
                                </li>
                                <li class="{{$link=="user"&&$sublink=="create"?'active':''}}">
                                    <a href="{{ route('admin.user.create') }}">
                                        <span class="sub-item">@lang('create')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item {{$link=="restaurant"?'active':''}}">
                        <a data-toggle="collapse" href="#restaurant">
                            <i class="fas fa-utensils"></i>
                            <p>@lang('restaurants')</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{$link=="restaurant"?'show':''}}" id="restaurant">
                            <ul class="nav nav-collapse">
                                <li class="{{$link=="restaurant"&&$sublink=="list"?'active':''}}">
                                    <a href="{{ route('admin.restaurant.list') }}">
                                        <span class="sub-item">@lang('list')</span>
                                    </a>
                                </li>
                                <li class="{{$link=="restaurant"&&$sublink=="create"?'active':''}}">
                                    <a href="{{ route('admin.restaurant.create') }}">
                                        <span class="sub-item">@lang('create')</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @elseif($user->role == "restaurant")
                    <li class="nav-item {{($main=="restaurant")?'active':''}}">
                        <a href="{{route('restaurant.list')}}">
                            <i class="fas fa-utensils"></i>
                            <p>@lang('restaurants')</p>
                        </a>
                    </li>
                @endif
                @if($user->role == "waiter")
                    <li class="nav-item {{($link=="tables")?'active':''}}">
                        <a href="{{route('waiter.tables')}}">
                            <i class="fas fa-table"></i>
                            <p>@lang('tables')</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
