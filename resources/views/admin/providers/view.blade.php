@extends('layouts.admin') 

@section('title', tr('view_providers')) 

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.providers.index') }}">{{tr('provider')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_providers')}}</span>
    </li>

@endsection 

@section('content')

<div class="row">

    <div class="col-md-12">

        <!-- Card group -->
        <div class="card-group">

            <!-- Card -->
            <div class="card mb-6">

                <!-- Card image -->
                <div class="view overlay">
                    <img class="card-img-top" src="{{$provider_details->picture}}">
                    <a href="#!">
                        <div class="mask rgba-white-slight"></div>
                    </a>
                </div>

                <!-- Card content -->
                <div class="card-body">

                    <div class="row"> 
                        <div class="col-md-6">
 
                            @if(Setting::get('is_demo_control_enabled') == NO )

                            <a href="{{ route('admin.providers.edit', ['provider_id' => $provider_details->id]) }}" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                            <a class="btn btn-danger btn-block" href="{{route('admin.providers.delete', ['provider_id' => $provider_details->id])}}" onclick="return confirm(&quot;{{tr('provider_delete_confirmation' , $provider_details->first_name)}}&quot;);" class="btn btn-danger btn-block">{{tr('delete')}}</a> 

                            @else

                                <a href="javascript:;" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                                <a class="btn btn-danger btn-block" href="javascript:;">{{tr('delete')}}</a> 

                            @endif 

                            @if($provider_details->is_verified == NO)  
                                    
                                <a class="btn btn-info btn-block" href="{{ route('admin.providers.verify', ['provider_id' => $provider_details->id]) }} ">{{tr('verify')}} 
                                </a>

                            @endif

                            @if($provider_details->status == PROVIDER_APPROVED)

                                <a class="btn btn-danger btn-block" onclick="return confirm(&quot;{{$provider_details->first_name}} - {{tr('provider_decline_confirmation')}}&quot;);" href="{{ route('admin.providers.status', ['provider_id' => $provider_details->id]) }}">{{tr('decline')}}</a> 

                            @else

                                <a class="btn btn-success btn-block" href="{{ route('admin.providers.status', ['provider_id' => $provider_details->id]) }}">{{tr('approve')}}</a> 

                            @endif
                            
                            <a href="{{ route('admin.spaces.index',['provider_id' => $provider_details->id])}}" class="btn btn-info btn-block">{{tr('parking_space')}}</a>

                        </div>

                        <div class="col-md-6">
                            
                            <a class="btn btn-success btn-block" href="{{ route('admin.provider_subscriptions.plans' , ['provider_id' => $provider_details->id]) }}" >{{ tr('plans') }}</a>

                            <a class="btn btn-info btn-block" href="{{ route('admin.bookings.index', ['provider_id' => $provider_details->id]) }}">
                                {{ tr('bookings') }} 
                            </a>

                            <a class="btn btn-warning btn-block" href="{{ route('admin.reviews.providers', ['provider_id' => $provider_details->id]) }}">
                                {{ tr('reviews') }} 
                            </a> 

                        </div>

                    </div>
                    <hr>

                    <div class="row">
                        <!-- Title -->
                        @if($provider_details->description)
                            <h4 class="card-title">{{tr('description')}}</h4>
                            <!-- Text -->
                            <p class="card-text">{{$provider_details->description}}</p>
                        @endif
                    </div>

                </div>
                <!-- Card content -->   

            </div>
            <!-- Card -->

            <!-- Card -->
            <div class="card mb-6">

                <!-- Card content -->
                <div class="card-body">

                    <div class="template-demo">

                        <table class="table mb-0">

                            <tbody>
                                <tr>
                                    <td class="pl-0"><b>{{ tr('name') }}</b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{$provider_details->name}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pl-0"><b>{{ tr('status') }}</b></td>

                                    <td class="pr-0  text-right">
                                        @if($provider_details->status == APPROVED)

                                            <span class="badge badge-success badge-md text-uppercase">{{tr('approved')}}</span> 

                                        @else

                                            <span class="badge badge-danger badge-md text-uppercase">{{tr('pending')}}</span>

                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pl-0"><b>{{ tr('email') }}</b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{$provider_details->email}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pl-0"><b>{{ tr('mobile') }}</b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{$provider_details->mobile}}</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"><b>{{ tr('device_type') }}</b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{$provider_details->device_type}}</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"><b>{{tr('payment_mode')}} </b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{$provider_details->payment_mode}}</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b>{{tr('timezone')}}</b></td>
                                    <td class="pr-0  text-right">
                                        {{$provider_details->timezone}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pl-0"> <b> {{ tr('account_name') }} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div> {{ $provider_billing_info->account_name ?? '' }} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('paypal_email')}} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div>{{ $provider_billing_info->paypal_email ?? '' }} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('account_no')}} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div>{{ $provider_billing_info->account_no ?? ''}} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('route_no')}} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div>{{ $provider_billing_info->route_no ?? '' }} </div>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>
                    <!-- </div> -->

                </div>
                <!-- Card content -->

            </div>

            <!-- Card -->

            <!-- Card -->
            <div class="card mb-6">

                <!-- Card content -->
                <div class="card-body">

                    <table class="table mb-0">
                        <tbody>
                             <tr>
                                <td class="pl-0"><b>{{ tr('is_email_verified') }}</b></td>

                                    <td class="pr-0  text-right">
                                        @if($provider_details->is_verified == PROVIDER_EMAIL_NOT_VERIFIED)

                                        <span class="card-text label label-rouded label-menu label-danger">{{ tr('no') }}</span> 

                                        @else

                                        <span class="card-text badge badge-success badge-md text-uppercase">{{ tr('yes') }}</span> 

                                        @endif
                                    </td>
                                </tr>

                                <tr>                                
                                    <td class="pl-0"><b>{{ tr('push_notification') }}</b></td>

                                    <td class="pr-0  text-right ">

                                        @if($provider_details->push_notification_status)

                                        <span class="card-text badge badge-success badge-md text-uppercase">{{tr('on')}}</span> 

                                        @else

                                        <span class="card-text badge badge-danger badge-md text-uppercase">{{tr('off')}}</span> 

                                        @endif

                                    </td>
                                </tr>   

                                <tr>                                
                                    <td class="pl-0"><b>{{ tr('email_notification') }}</b></td>

                                     <td class="pr-0 text-right">
                                        @if($provider_details->email_notification_status)

                                        <span class="card-text badge badge-success badge-md text-uppercase">{{tr('on')}}</span> 

                                        @else

                                        <span class="card-text badge badge-danger badge-md text-uppercase">{{tr('off')}}</span> 

                                        @endif
                                    </td>
                                </tr>
                             
                                <tr>
                                    <td class="pl-0"> <b> {{ tr('work') }} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div> {{$provider_details->work}} </div>
                                    </td>
                                </tr> 

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('school') }} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div> {{$provider_details->school}} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{tr('full_address')}} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div> {{$provider_details->full_address}} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('languages') }} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div> {{$provider_details->languages}} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"><b>{{ tr('login_by') }}</b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{ $provider_details->login_by }}</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0">
                                        <b>{{tr('register_type')}} </b></td>
                                    <td class="pr-0 text-right">
                                        <div>{{$provider_details->register_type}}</div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('created_at') }} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div> {{ common_date($provider_details->created_at) }} </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-0"> <b> {{ tr('updated_at')}} </b> </td>
                                    <td class="pr-0  text-right">
                                        <div>{{ common_date($provider_details->updated_at) }} </div>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>
                    <!-- </div> -->

                </div>
                <!-- Card content -->

            </div>

            <!-- Card -->

        </div>
        <!-- Card group -->

    </div>

</div>
@endsection