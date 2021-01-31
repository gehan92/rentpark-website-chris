@extends('layouts.admin') 

@section('title', tr('payments'))

@section('breadcrumb')

<li class="breadcrumb-item active" aria-current="page">
    <span>{{tr('view_provider_subscription_payment')}}</span>
</li>

@endsection 

@section('content')

<div class="col-lg-12 grid-margin stretch-card">
    
    <div class="card">

        <div class="card-header bg-card-header ">
            <h4 class="">{{tr('view_provider_subscription_payment')}} </h4>
        </div>

        <div class="card-body">

            <div class="row">

                <div class=" col-sm-6 table-responsive">
                    
                    <h6 class="card-title">{{ tr('details') }}</h6>

                    <table class="table table-bordered table-striped tab-content">
                       
                        <tbody>
                      
                            <tr>
                                <td>{{ tr('provider')}} </td>
                                <td>
                                    <a href="{{ route('admin.providers.view' , ['provider_id' => $provider_subscription_payment->provider_id ]) }}">
                                    {{ $provider_subscription_payment->providerDetails->name ?? tr('provider_not_avail')}}
                                    </a>
                                </td>
                            </tr> 
                            <tr>
                                <td>{{ tr('provider_subscription')}} </td>
                                <td>
                                    <a href="{{ route('admin.provider_subscriptions.view' ,['provider_subscription_id' => $provider_subscription_payment->provider_subscription_id]) }}">{{ $provider_subscription_payment->providerSubscriptionDetails->title ?? tr('provider_subscription_not_avail') }}</a>
                                </td>
                            </tr> 
                            <tr>
                                <td>{{ tr('payment_id') }}</td>
                                <td>{{ $provider_subscription_payment->payment_id }}</td>
                            </tr>
                            <tr>
                                <td>{{tr('paid_at')}} </td>
                                <td>{{common_date($provider_subscription_payment->updated_at, Auth::guard('admin')->user()->timezone)}}</td>
                            </tr>
                            <tr>
                                <td>{{ tr('plan') }}</td>
                                <td>{{plan_text( $provider_subscription_payment->providerSubscriptionDetails->plan ?? '0',  $provider_subscription_payment->providerSubscriptionDetails->plan_type ?? '')}}</td>
                            </tr>

                            <tr>
                                <td>{{ tr('is_current_subscription') }}</td>
                                <td>
                                    @if( $provider_subscription_payment->is_current_subscription ) 
                                        <span class="badge badge-success badge-fw">{{ tr('yes') }}</span>
                                    @else
                                        <span class="badge badge-danger badge-fw">{{ tr('no') }}</span>

                                    @endif
                                </td>
                            </tr>


                            <tr>
                                <td>{{tr('expiry_at')}}</td>
                                <td>{{common_date($provider_subscription_payment->expiry_date, Auth::guard('admin')->user()->timezone)}}</td>
                            </tr>

                            <tr>
                                <td>{{ tr('subscription_amount') }}</td>
                                <td>{{ formatted_amount($provider_subscription_payment->subscription_amount) }}</td>
                            </tr>

                            <tr>
                                <td>{{ tr('paid_amount') }}</td>
                                <td>{{ formatted_amount($provider_subscription_payment->paid_amount) }}</td>
                            </tr>

                            <tr>
                                <td>{{ tr('status') }}</td>
                                <td>
                                    @if($provider_subscription_payment->status ) 
                                           
                                        <span class="badge badge-success badge-fw">{{ tr('paid')}}</span>

                                    @else
                                           
                                        <span class="badge badge-danger badge-fw">{{ tr('not_paid')}}</span>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td>{{ tr('is_cancelled') }}</td>
                                <td>
                                    @if( $provider_subscription_payment->is_cancelled ) 
                                        <span class="badge badge-success badge-fw">{{ tr('yes') }}</span>
                                    @else
                                        <span class="badge badge-danger badge-fw">{{ tr('no') }}</span>

                                    @endif
                                </td>
                            </tr>

                            @if( $provider_subscription_payment->is_cancelled == YES)
                                <tr>
                                    <td>{{ tr('reason') }}</td>

                                    <td>
                                        {{ $provider_subscription_payment->cancelled_reason }}                                        
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>{{ tr('subscribed_by')}} </td>
                                <td>
                                    {{ $provider_subscription_payment->subscribed_by ?? tr('no_subscriber_avail')}}
                                    </a>
                                </td>
                            </tr> 

                            <tr>
                                <td>{{tr('created_at')}}</td>
                                <td>{{common_date($provider_subscription_payment->created_at, Auth::guard('admin')->user()->timezone)}}</td>
                            </tr>

                            <tr>
                                <td>{{tr('updated_at')}}</td>
                                <td>{{common_date($provider_subscription_payment->updated_at, Auth::guard('admin')->user()->timezone)}}</td>
                            </tr>


                        </tbody>

                    </table>

                </div>


                <div class=" col-sm-6 table-responsive">
                    
                    <h6 class="card-title">{{ tr('picture') }}</h6>

                    <img src="{{ $provider_subscription_payment->providerDetails->picture }}" class="picture">                    
                    
                </div>

            </div>

        </div>

    </div>

</div>

@endsection