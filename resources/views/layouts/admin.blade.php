<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ Setting::get('site_name')}} - @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css') }}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/font-awesome/css/font-awesome.min.css') }}" />
    

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/jquery-bar-rating/dist/themes/fontawesome-stars.css') }}">

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css')}}" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/summernote/dist/summernote-bs4.css')}}">

    <link rel="stylesheet" href="{{ asset('admin-assets/css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('admin-assets/css/custom.css') }}">


    <link rel="shortcut icon" href="{{ Setting::get('site_icon')}}" />

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/jquery-bar-rating/dist/themes/css-stars.css') }}">
    
    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/jquery-bar-rating/dist/themes/fontawesome-stars-o.css')}} ">

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/jquery-bar-rating/dist/themes/fontawesome-stars.css') }}">
  
    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/morris.js/morris.css') }}" />

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/lightgallery/dist/css/lightgallery.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/fullcalendar/dist/fullcalendar.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/c3/c3.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('admin-assets/node_modules/icheck/skins/all.css') }}" />

    <style>
        label {
            text-transform: uppercase;
        }
        .picture{
            height: 408px;

            width: 500px;
        }
    </style>

    @yield('styles')

    <?php echo Setting::get('header_scripts'); ?>

</head>

<body class="sidebar-fixed sidebar-dark">

    <div class="container-scroller">

        @include('layouts.admin.header')

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">

            <div class="row row-offcanvas row-offcanvas-right">

                <!-- partial:_sidebar-->
                @include('layouts.admin.sidebar')
                <!-- partial -->

                <!-- content-wrapper -->
                <div class="content-wrapper">
                    
                    @if(Setting::get('is_demo_control_enabled') == YES)
            
                        <div class="alert alert-warning" role="alert">
                            {{tr('admin_control_enabled_alert')}}
                            <a href="https://rentcubo.com/contact/">Contact Us!</a>
                        </div>

                    @endif  
                                        
                    <!-- partial:_breadcrum -->
                    <div class="col-md-12 grid-margin stretch-card">

                        <div class="template-demo">

                            <nav aria-label="breadcrumb" role="navigation">
                                
                                <ol class="breadcrumb breadcrumb-custom">

                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{tr('home')}}</a></li>
                                    
                                    @yield('breadcrumb')
                                    
                                </ol>

                            </nav>

                        </div>

                    </div>
                    <!-- partial -->

                    @include('notifications.notify')

                    @yield('content')

                </div>
                <!-- content-wrapper ends -->

                <!-- partial:_footer -->

                @include('layouts.admin.footer')

                <!-- partial -->

            </div>
            <!-- row-offcanvas ends -->
        </div>
        <!-- page-body-wrapper ends -->
    
    </div>

    <!-- container-scroller -->

    @include('layouts.admin.scripts')

    @yield('scripts')

    <?php echo Setting::get('body_scripts'); ?>

    <!-- End custom js for this page-->
</body>

</html>