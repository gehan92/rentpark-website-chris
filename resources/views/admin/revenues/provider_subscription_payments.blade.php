@extends('layouts.admin') 

@section('title', tr('subscription_payments'))

@section('breadcrumb')
    <li class="breadcrumb-item"><a>{{tr('revenues')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('subscription_payments')}}</span>
    </li>
           
@endsection 

@section('content') 

	<div class="col-lg-12 grid-margin">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('subscription_payments')}}</h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
                
                	<table id="order-listing" class="table">
                        
                        <thead>
                            <tr>
                                <th>{{ tr('id') }}</th>
                                <th>{{ tr('provider') }}</th>
                                <th>{{ tr('subscriptions') }}</th>
                                <th>{{ tr('payment_id') }}</th>
                                <th>{{ tr('plan') }}</th>
                                <th>{{ tr('total') }} </th>
                                <th>{{ tr('status') }}</th>
                                <th>{{ tr('is_cancelled') }}</th>
                                <th>{{ tr('action') }}</th>
                            </tr>

                        </thead>
                                               
                        <tbody>

                            @foreach($provider_subscription_payments as $i => $provider_subscription_payment_details)

                                <tr>
                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{ route('admin.providers.view' , ['provider_id' => $provider_subscription_payment_details->provider_id ]) }}">{{ $provider_subscription_payment_details->providerDetails->name ?? tr('provider_not_avail') }}</a>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.provider_subscriptions.view' ,['provider_subscription_id' => $provider_subscription_payment_details->provider_subscription_id]) }}">{{ $provider_subscription_payment_details->providerSubscriptionDetails->title ?? tr('provider_subscription_not_avail') }}</a>
                                    </td>
                                  
                                    <td>
                                        {{ $provider_subscription_payment_details->payment_id }}
                                        <br>
                                        <small>{{tr('paid_at')}}: {{common_date($provider_subscription_payment_details->updated_at, Auth::guard('admin')->user()->timezone)}}</small>
                                    </td>

                                    <td>
                                        {{plan_text( $provider_subscription_payment_details->providerSubscriptionDetails->plan ?? '0',  $provider_subscription_payment_details->providerSubscriptionDetails->plan_type ?? '')}}

                                        <br>
                                        <small class="text-success">{{tr('expiry_at')}}: {{common_date($provider_subscription_payment_details->expiry_date, Auth::guard('admin')->user()->timezone)}}</small>

                                    </td>

                                    <td>
                                        {{ formatted_amount($provider_subscription_payment_details->paid_amount) }}
                                    </td>

                                    <td>
                                        @if($provider_subscription_payment_details->status ) 
                                           
                                           <span class="badge badge-success badge-fw">{{ tr('paid')}}</span>

                                        @else
                                           
                                            <span class="badge badge-danger badge-fw">{{ tr('not_paid')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if( $provider_subscription_payment_details->is_cancelled ) 
                                            <span class="badge badge-success badge-fw">{{ tr('yes') }}</span>
                                        @else
                                            <span class="badge badge-danger badge-fw">{{ tr('no') }}</span>

                                        @endif
                                    </td>

                                     <td>
                                            <a class="btn btn-primary" href="{{ route('admin.provider.subscriptions.payments.view', ['id' => $provider_subscription_payment_details->id] )}}">
                                                {{tr('view')}}
                                            </a> 
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