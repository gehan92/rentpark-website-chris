<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ Setting::get('site_name') }}</title>

    <!-- Bootstrap core CSS -->
    <link href="{{asset('landing-assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="{{asset('landing-assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('landing-assets/vendor/simple-line-icons/css/simple-line-icons.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="{{asset('landing-assets/device-mockups/device-mockups.min.css')}}">

    <!-- Custom styles for this template -->
    <link href="{{asset('landing-assets/css/new-age.css')}}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ Setting::get('site_icon') }}" />

    <?php echo Setting::get('header_scripts'); ?>

</head>

<body>

    @yield('content')

    <!-- Bootstrap core JavaScript -->
    <script src="{{asset('landing-assets/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('landing-assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Plugin JavaScript -->
    <script src="{{asset('landing-assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for this template -->
    <script src="{{asset('landing-assets/js/new-age.min.js')}}"></script>

    <?php echo Setting::get('google_analytics'); ?>

    <?php echo Setting::get('body_scripts'); ?>

</body>

</html>