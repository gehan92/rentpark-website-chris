@extends('layouts.admin') 

@section('title', tr('dashboard'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a>{{tr('revenues')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('dashboard')}}</span>
    </li>

@endsection 

@section('content')

<div class="row">


    <!-- Total Revenue details begins -->
    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="icon-user icon-lg text-success"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('admin_earnings') }}</p>
                        <h6>{{ formatted_amount($data->total_admin_amount) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="fa fa-group icon-lg text-warning"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('providers_earnings') }}</p>
                        <h6>{{ formatted_amount($data->total_provider_amount) }} </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="mdi mdi-wallet icon-lg text-info"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('total_earnings') }}</p>
                        <h6>{{ formatted_amount($data->total_amount) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="fa fa-money icon-lg text-warning"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('today_earnings') }}</p>
                        <h6>{{ formatted_amount($data->total_today_amount) }} </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Revenue details ends -->


    <!-- last x(10) days Revenue details begins -->
    <div class="col-lg-12 grid-margin stretch-card">

        <div class="card">

            <div class="card-body">

                <h4 class="card-title"> {{ tr('last_x_days') }} {{ tr('bookings')}} {{ tr('revenues')}}</h4>

                <canvas id="barChart" style="height:230px"></canvas>
                
            </div>

        </div>

    </div>
    <!-- Last x(10) days Revenue details ends -->


    <!-- Recent booking of users details begins -->
    <div class="col-lg-12 grid-margin stretch-card">
            
        <div class="card">
            
            <div class="card-header general-box-info">
                <h4>{{ tr('recent_bookings') }}</h4>
            </div>
            
            <div class="card-body">
                
                @if(count($data->recent_bookings) > 0)

                    @foreach($data->recent_bookings as $i => $recent_booking_details)

                        <div class="list d-flex align-items-center border-bottom py-3">
                            <a href="{{ route('admin.users.view', ['user_id' => $recent_booking_details->user_id]) }}">
                                <img class="img-sm rounded-circle" src="{{ $recent_booking_details->user_picture }}" alt="">
                            </a>

                            <div class="wrapper w-100 ml-3">
                                <a href="{{ route('admin.users.view', ['user_id' => $recent_booking_details->user_id]) }}"><p class="mb-0"><b>{{ $recent_booking_details->user_name }}</b> </a>
                                <br>
                                <small class="text-muted ml-auto">{{ tr('host') }} : <a href="{{ route('admin.spaces.view', ['host_id' => $recent_booking_details->host_id]) }}">{{ $recent_booking_details->host_name }}</a></small>
                                </p></a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <small class="mb-0 text-muted"><i class="mdi mdi-clock mr-1"></i>{{ $recent_booking_details->user_create}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                   @endforeach

                @else

                    <p class="text-muted">{{tr('no_result_found')}}</p>

                @endif


            </div>
            
        </div>

    </div>
    <!-- Recent booking of users details ends -->

</div>

@endsection

@section('scripts')

    <script type="text/javascript">
        var options = {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            legend: {
                display: false
            },
            elements: {
                point: {
                    radius: 0
                }
            }

        };
        <?php $currency = Setting::get('currency', '$'); ?>
        var data = {
            // labels: ["2013", "2014", "2014", "2015", "2016", "2017"],
            labels: [<?php 
                
                foreach ($data->analytics->last_x_days_revenues as $key => $value) {
                    echo '"'.$value->date.'"'.',';
                }

            ?> ],
            datasets: [{
                label: 'Booking Revenue',
                // data: [10, 19, 3, 5, 2, 3,20,25, 23,30],
                data: [<?php 
                    foreach ($data->analytics->last_x_days_revenues as $key => $value) {
                        echo $value->total_earnings.',';
                    }

                    ?> ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',

                    'rgba(135, 13, 116, 0.2)',
                    'rgba(198, 189, 90, 0.2)',
                    'rgba(18, 242, 233, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',

                    'rgba(135, 13, 116, 1)',
                    'rgba(198, 189, 90, 1)',
                    'rgba(18, 242, 233, 1)',
                    'rgba(255, 159, 64, 1)',
                ],
                borderWidth: 1
            }]
        };

        // Get context with jQuery - using jQuery's .get() method.
        if ($("#barChart").length) {
            var barChartCanvas = $("#barChart").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var barChart = new Chart(barChartCanvas, {
                type: 'bar',
                data: data,
                options: options
            });
        }
    </script>

@endsection