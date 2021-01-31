@extends('layouts.admin') 

@section('title', tr('provider_subscription_payments'))

@section('breadcrumb')

    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('provider_subscription_payments')}}</span>
    </li>
           
@endsection 

@section('content') 

	<div class="col-lg-12 grid-margin">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('provider_subscription_payments')}}</h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                	<table id="order-listing" class="table">
                                             
                        <thead>
                            <tr>
                                <th>{{ tr('id') }}</th>
                                <th>{{ tr('name') }}</th>
                                <th>{{ tr('provider_subscription') }}</th>
                                <th>{{ tr('payment_id') }}</th>
                                <th>{{ tr('amount') }}</th>
                                <th>{{ tr('expiry_date') }}</th>
                                <th style="display: none">{{ tr('reason') }}</th>
                                <th style="display: none">{{ tr('action') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($provider_subscription_payments as $i => $provider_subscription_payment_details)

                                <tr>
                                    
                                    <td>{{ $i+1 }}</td>

                                    <td>
                                        <a href="{{ route('admin.providers.view', ['provider_id' => $provider_subscription_payment_details->providerDetails->id ]) }}">{{ $provider_subscription_payment_details->providerDetails->name ?? tr('provider_not_avail') }} </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.provider_subscriptions.view', ['provider_subscription_id' => $provider_subscription_payment_details->providerSubscriptionDetails->id ]) }}"> {{ $provider_subscription_payment_details->providerSubscriptionDetails->title ?? tr('subscription_not_avail') }} </a> 
                                    </td>

                                    <td>{{ $provider_subscription_payment_details->payment_id }}</td>

                                    <td>{{ formatted_amount($provider_subscription_payment_details->paid_amount) }}</td>

                                    <td>{{ date('d M Y',strtotime($provider_subscription_payment_details->expiry_date)) }}</td>

                                    <td style="display: none">{{ $provider_subscription_payment_details->cancelled_reason }}</td>

                                    <td class="text-center" style="display :none">

                                        @if($i == 0 && !$provider_subscription_payment_details->is_cancelled && $provider_subscription_payment_details->status == PAID_STATUS) 
                                        <a data-toggle="modal" data-target="#{{ $provider_subscription_payment_details->id }}_cancel_subscription" class="pull-right btn btn-sm btn-danger">{{ tr('cancel_subscription') }}</a>

                                        @elseif($i == 0 && $provider_subscription_payment_details->is_cancelled && $provider_subscription_payment_details->status == PAID_STATUS)

                                            <?php $enable_subscription_notes = tr('enable_subscription_notes') ; ?>
                                        
                                            <a onclick="return confirm('{{ $enable_subscription_notes }}')" href="{{ route('admin.providers.subscriptions.enable', ['provider_id' => $provider_subscription_payment_details->provider_id]) }}" class="pull-right btn btn-sm btn-success">{{ tr('enable_subscription') }}</a>

                                        @else
                                            -       
                                        @endif
                                    </td>

                                </tr>

                                <div class="modal fade error-popup" id="{{ $provider_subscription_payment_details->id }}_cancel_subscription" role="dialog">

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form method="post" action="{{ route('admin.providers.subscriptions.cancel', ['provider_id' => $provider_subscription_payment_details->provider_id]) }}">

                                                <div class="modal-body">

                                                    <div class="media">

                                                        <div class="media-body">

                                                           <h4 class="media-heading">{{ tr('reason') }} *</h4>

                                                           <textarea rows="5" name="cancel_reason" id='cancel_reason' required style="width: 100%"></textarea>

                                                       </div>

                                                    </div>

                                                    <div class="text-right">

                                                        <br>

                                                       <button type="submit" class="btn btn-primary top">{{ tr('submit') }}</button>

                                                   </div>

                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </tbody>

                    </table>
              
                </div>

            </div>

        </div>

    </div>

    @if(count($provider_subscriptions) > 0)
                   
        <div class="row pl-2" >
      
            @foreach($provider_subscriptions as $s => $provider_subscription_details)
                
                <div class="col-md-4 col-lg-4 col-sm-6 col-xs-12">
                   
                    <div class="card card-body grid-margin ">

                        <div class="thumbnail">

                            <div class="caption">

                                <center><h4 style="margin: 10px">
                                    <a href="{{ route('admin.provider_subscriptions.view', ['provider_subscription_id' => $provider_subscription_details->id])}}" target="_blank">{{ $provider_subscription_details->title }}</a>
                                </h4></center>

                                <hr>

                            
                                @if($provider_subscription_details->description)
                                    <h4><b>{{ tr('description') }} : </b></h4>

                                    <div class="subscription-desc">
                                        <?php echo $provider_subscription_details->description; ?>
                                    </div>
                                @endif
                                <br>

                                <p>
                                    <span class="btn btn-danger pull-left" style="cursor: default;">{{ formatted_amount( $provider_subscription_details->amount) }} / {{ $provider_subscription_details->plan }} M</span>

                                    <a href="{{ route('admin.providers.subscriptions.plans.save' , ['provider_subscription_id' => $provider_subscription_details->id, 'provider_id' => $provider_id]) }}" class="btn btn-success pull-right">{{ tr('choose') }}</a>
                                </p>
                                <br>
                            
                            </div>
                        
                        </div>

                    </div>
               
                </div>
            
            @endforeach

        </div>              

    @endif
    

@endsection