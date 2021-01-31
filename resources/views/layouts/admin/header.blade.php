
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row navbar-dark">
    
    <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">

        <a href="javascript:;" class="main-header-logo">{{Setting::get('site_name')}}</a>

        <a class="navbar-brand brand-logo-mini" href="#"><img src="{{ Setting::get('site_logo') }}" alt="{{Setting::get('site_name')}}" style="font-size: 0.75em"/></a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-center">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
   
        <ul class="navbar-nav navbar-nav-right">

            <li class="nav-item d-none d-lg-block">
                <a class="btn btn-inverse-light btn-fw" href="{{Setting::get('frontend_url')}}" target="_blank">
                    Visit Website
                </a>
            </li>
        
            <li class="nav-item dropdown d-none d-lg-flex">

                <a class="nav-link dropdown-toggle" id="languageDropdown" href="#" data-toggle="dropdown">
                    <!-- <i class="flag-icon flag-icon-gb"></i>  -->
                  <img alt="" style="width: auto; max-height: 50px" class="img-circle img-responsive" src="{{ Auth::guard('admin')->user() ? Auth::guard('admin')->user()->picture : '' }}" />
                    {{ Auth::guard('admin')->user() ? Auth::guard('admin')->user()->name :'' }}
                </a>

                <div class="dropdown-menu navbar-dropdown" aria-labelledby="languageDropdown">
                    <a class="dropdown-item font-weight-medium" href="{{ route('admin.profile')}}">
                        <i class="icon-user menu-icon"></i>  
                        {{tr('profile')}}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item font-weight-medium" href="{{ route('admin.settings')}}">
                        <i class="icon-settings menu-icon"></i> 
                        {{tr('settings')}}
                    </a>
                  
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item font-weight-medium" href="{{ route('admin.logout') }}">
                        <i class="fa fa-power-off menu-icon"></i>  
                        {{tr('logout')}}
                    </a>
                </div>
            </li>

        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>

    </div>

</nav>

