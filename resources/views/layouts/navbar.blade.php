<!-- Main navbar -->
<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">

        <a href="{{url('/')}}" class="logo w-100 text-cente d-flex text-white align-items-center">
            <img src="{{asset('assets/img/logo.png')}}" alt="navbar brand" class="navbar-brand" style="margin-right: 5px; height: 75%">
            {{env('APP_NAME')}}
        </a>
        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="icon-menu"></i>
            </button>
        </div>
    </div>
    <!-- End Logo Header -->

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-expand-lg" data-background-color="white">
        <div class="container-fluid d-flex justify-content-between">
            @php
                $user = \Illuminate\Support\Facades\Auth::user();
            @endphp
            <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                <li class="nav-item dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{isset($user->avatar)?$user->avatar:asset('assets/img/profile.jpg')}}" onerror="src='{{asset('assets/img/profile.jpg')}}'" class="avatar-img rounded-circle">
                        </div>
                    </a>
                    @if(isset($user))
                        <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg"><img src="{{isset($user->avatar)?$user->avatar:asset('assets/img/profile.jpg')}}" onerror="src='{{asset('assets/img/profile.jpg')}}'" class="avatar-img rounded"></div>
                                    <div class="u-text">
                                        <h4>{{$user->name}}</h4>
                                        <p class="text-muted">{{$user->email}}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('profile') }}">@lang('my_profile')</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">@lang('logout')</a>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </ul>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>
<!-- /Main navbar -->
