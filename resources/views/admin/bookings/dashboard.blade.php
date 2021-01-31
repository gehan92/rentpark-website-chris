@extends('layouts.admin') 

@section('title', tr('dashboard'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{tr('bookings')}}</a></li>
  
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('dashboard') }}</span>
    </li>
           
@endsection 

@section('content')

<div class="row">

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">                 
                <a href="{{route('admin.bookings.index')}}" target="_blank">
                    <div class="d-flex align-items-center justify-content-md-center">
                        <i class="icon-calendar icon-lg text-primary"></i>
                        <div class="ml-3">
                            <p class="mb-0">{{ tr('total_bookings') }}</p>
                            <h6>{{ $data->total_bookings }}</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>      

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">                 
                <a href="{{route('admin.bookings.index', ['status' =>BOOKING_COMPLETED ])}}" target="_blank">
                    <div class="d-flex align-items-center justify-content-md-center">
                        <i class="icon-calendar icon-lg text-success"></i>
                        <div class="ml-3">
                            <p class="mb-0">{{ tr('bookings_completed') }}</p>
                            <h6>{{ $data->bookings_completed }}</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>    

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">                
                <a href="{{route('admin.bookings.index', ['status' => BOOKING_CANCELLED_BY_PROVIDER])}}" target="_blank">
                    <div class="d-flex align-items-center justify-content-md-center">
                        <i class="icon-calendar icon-lg text-danger"></i>
                        <div class="ml-3">
                            <p class="mb-0">{{ tr('total_bookings_cancelled_by_provider') }}</p>
                            <h6>{{ $data->bookings_cancelled_by_provider }}</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>    

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <a href="{{route('admin.bookings.index', ['status' => BOOKING_CANCELLED_BY_USER])}}" target="_blank">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="icon-calendar icon-lg text-danger"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('total_bookings_cancelled_by_user') }}</p>
                        <h6>{{ $data->bookings_cancelled_by_user }}</h6>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>    

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <a href="{{route('admin.bookings.index', ['status' => BOOKING_CHECKIN])}}" target="_blank">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="icon-calendar icon-lg text-info"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('today_total_checkin') }}</p>
                        <h6>{{ $data->today_bookings_checkin }}</h6>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <a href="{{route('admin.bookings.index', ['status' => BOOKING_CHECKOUT])}}" target="_blank">
                <div class="d-flex align-items-center justify-content-md-center">
                    <i class="icon-calendar icon-lg text-warning"></i>
                    <div class="ml-3">
                        <p class="mb-0">{{ tr('today_total_checkout') }}</p>
                        <h6>{{ $data->today_bookings_checkout }}</h6>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection