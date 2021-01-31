<!DOCTYPE html>
<html lang="en">
<head>

    <title>{{ Setting::get('site_name') }}</title>

    <meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="{{ asset('admin-assets/css/style.css') }}">

    <link rel="shortcut icon" href="{{ Setting::get('site_icon') }}" />

    <?php echo Setting::get('header_scripts'); ?>

</head>

<body>

    @yield('content')

    <?php echo Setting::get('google_analytics'); ?>

    <?php echo Setting::get('body_scripts'); ?>

</body>

</html>