@extends('layouts.app')

@section('content')

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="#page-top">
            {{Setting::get('site_name')}}
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="{{route('admin.login')}}" target="_blank">Admin</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="{{Setting::get('frontend_url')}}">Demo</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="https://rentcubo.com/contact/">Contact Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="masthead">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-lg-7 my-auto">
                <div class="header-content mx-auto">
                    <h1 class="mb-5">Launch your vacation rental business with {{Setting::get('site_name')}}</h1>
                    <a href="{{url('/')}}" class="btn btn-full btn-xl js-scroll-trigger"> Try The Admin Demo!</a>
                    <br>
                    <br>
                    <a href="https://rentcubo.com/airbnb-clone-script/" class="btn btn-outline btn-xl js-scroll-trigger">Lite Free Download!</a>
                </div>
            </div>
            <div class="col-lg-5 my-auto">
                <div class="device-container">
                    <div class="device-mockup iphone6_plus portrait white">
                        <div class="device">
                            <div class="screen">
                                <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                                <img src="{{asset('landing-assets/img/demo-screen-1.jpg')}}" class="img-fluid" alt="">
                            </div>
                            <div class="button">
                                <!-- You can hook the "home button" to some JavaScript events or just remove it -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="download text-center" id="download">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h2 class="section-heading">Discover what all the buzz is about!</h2>
                <p>Our app is available on any mobile device! Download now to get started!</p>

                <h5>User Demo</h5>
                <hr>
                <div class="badges">
                    <a class="badge-link" href="{{Setting::get('playstore_user')}}">
                        <img src="{{asset('landing-assets/img/google-play-badge.svg')}}" alt="">
                    </a>

                    <a class="badge-link" href="{{Setting::get('appstore_user')}}">
                        <img src="{{asset('landing-assets/img/app-store-badge.svg')}}" alt="">
                    </a>

                </div>

                <hr>

                <h5>Provider Demo</h5>
                <hr>
                <div class="badges">
                    <a class="badge-link" href="{{Setting::get('playstore_provider')}}">
                        <img src="{{asset('landing-assets/img/google-play-badge.svg')}}" alt="">
                    </a>
                    <a class="badge-link" href="{{Setting::get('appstore_provider')}}">
                        <img src="{{asset('landing-assets/img/app-store-badge.svg')}}" alt="">
                    </a>

                </div>

            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <p>&copy; {{Setting::get('site_name')}} {{date('Y')}}. All Rights Reserved.</p>
    </div>
</footer>

@endsection
