@extends('layouts.admin') 

@section('title', tr('history'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">{{tr('bookings')}}</a></li>
  
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('history') }}</span>
    </li>
           
@endsection 

@section('content')

    <div class="col-lg-12 grid-margin stretch-card">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('history')}}</h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
                    
                    <table id="order-listing" class="table">
                        
                        <thead>
                            <tr>
                                <th>{{tr('s_no')}}</th>
                                <th>{{tr('user') }}</th>
                                <th>{{tr('provider') }}</th>
                                <th>{{tr('parking_space') }}</th>
                                <th>{{tr('checkin') }}-{{tr('checkout') }}</th>
                                <th>{{tr('status')}}</th>
                                <th>{{tr('action')}}</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                         
                            @foreach($bookings as $i => $booking_details)

                                <tr>
                                    <td>{{$i+1}}</td>

                                    <td>
                                        @if($booking_details->userDetails->name)
                                            <a href="{{ route('admin.users.view',['user_id' => $booking_details->user_id ])}}"> {{ $booking_details->userDetails->name}}</a>
                                        @else
                                            {{tr('user_not_avail')}}
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.providers.view',['provider_id' => $booking_details->provider_id])}}">{{ $booking_details->providerDetails->name ?? tr('provider_not_avail')}}</a>
                                    </td>

                                    <td> 
                                        @if(empty($booking_details->host_name))

                                            {{ tr('host_not_avail') }}
                                        
                                        @else
                                            <a href="{{ route('admin.spaces.view',['host_id' => $booking_details->host_id])}}">{{$booking_details->hostDetails->host_name ?? tr('host_not_avail') }} </a>
                                        @endif

                                    </td>

                                    <td>
                                        {{common_date($booking_details->checkin, Auth::guard('admin')->user()->timezone, 'd M Y')}}
                                        -
                                        {{common_date($booking_details->checkout, Auth::guard('admin')->user()->timezone, 'd M Y')}}

                                    </td>
                                  
                                    <td>                                    
                                        <span class="text-info">{{ booking_status( $booking_details->status) }}</span>
                                    </td>
                                   
                                    <td>   
                                        <a class="btn btn-primary" href="{{ route('admin.bookings.view', ['booking_id' => $booking_details->id])}}"><i class="fa fa-eye"></i>{{tr('view')}}</a>
                                        
                                    </td>

                                </tr>
                                
                            @endforeach
                                                                 
                        </tbody>
                    
                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection