@extends('layouts.admin') 

@section('title', tr('view_provider_subscriptions'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="#">{{tr('provider_subscriptions')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_provider_subscriptions') }}</span>
    </li>
           
@endsection 

@section('content')

    <div class="col-lg-12 grid-margin stretch-card">
      
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('view_provider_subscriptions')}}
                    <a class="btn btn-secondary pull-right" href="{{route('admin.provider_subscriptions.create')}}">
                        <i class="fa fa-plus"></i>{{tr('add_provider_subscription')}}
                    </a>
                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
                 
                    <table id="order-listing" class="table">
                 
                        <thead>
                            <tr>
                                <th>{{ tr('id') }}</th>
                                <th>{{ tr('title') }}</th>
                                <th>{{ tr('amount') }}</th>
                                <th>{{ tr('plan') }}</th> 
                                <!-- <th>{{ tr('is_popular') }}</th> -->
                                <th>{{ tr('subscribers') }}</th>
                                <th>{{ tr('status') }}</th>
                                <th>{{ tr('action') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($provider_subscriptions as $i =>  $provider_subscription_details)

                                <tr>

                                    <td>{{ $i+1 }}</td>

                                    <td>    
                                        <a href="{{ route('admin.provider_subscriptions.view', ['provider_subscription_id' => $provider_subscription_details->id])}}" class=""> {{ $provider_subscription_details->title }} </a> 
                                    </td>

                                    <td>
                                        {{ formatted_amount($provider_subscription_details->amount) }}
                                    </td>

                                    <td>
                                        <span class="label label-success">{{$provider_subscription_details->plan}}
                                        </span>
                                    </td>

                                    <td style="display: none;">

                                        @if($provider_subscription_details->is_popular == YES )

                                            <span class="label label-success">{{ tr('yes') }}</span> 
                                        @else
                                            <span class="label label-warning">{{ tr('no') }}</span> 

                                        @endif

                                    </td>

                                    <td>
                                        <a href="{{ route('admin.provider_subscriptions.payments' ,['provider_subscription_id' => $provider_subscription_details->id])}}" class="btn btn-success btn-xs">

                                            {{ $provider_subscription_details->subscriptionPayments->count()}} 

                                        </a>
                                    </td>

                                    <td>

                                        @if($provider_subscription_details->status == USER_APPROVED)

                                            <span class="badge badge-outline-success">{{ tr('approved') }} </span>

                                        @else

                                            <span class="badge badge-outline-danger">{{ tr('declined') }} </span>

                                        @endif

                                    </td>

                                    <td>     

                                        <div class="template-demo">

                                            <div class="dropdown">

                                                <button class="btn btn-outline-primary  dropdown-toggle" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{tr('action')}}
                                                </button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">
                                                   
                                                <a class="dropdown-item" href="{{ route('admin.provider_subscriptions.view',['provider_subscription_id' => $provider_subscription_details->id] ) }}">{{tr('view')}}</a>
                                                  
                                                <a class="dropdown-item" href="{{ route('admin.provider_subscriptions.view',['provider_subscription_id' => $provider_subscription_details->id] ) }}"> {{tr('view')}} </a>

                                                @if(Setting::get('is_demo_control_enabled') == NO)

                                                    <a class="dropdown-item" href="{{ route('admin.provider_subscriptions.edit',['provider_subscription_id' => $provider_subscription_details->id] ) }}">
                                                        {{tr('edit')}}
                                                    </a>
                                                  
                                                    <a class="dropdown-item" href="{{route('admin.provider_subscriptions.delete',['provider_subscription_id' => $provider_subscription_details->id] )}}" 
                                                    onclick="return confirm(&quot;{{tr('provider_subscription_delete_confirmation' , $provider_subscription_details->title)}}&quot;);">
                                                        {{tr('delete')}}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="javascript:;">{{tr('edit')}}</a>
                                                  
                                                    <a class="dropdown-item" href="javascript:;">{{tr('delete')}}</a> 

                                                @endif
                                               
                                                <div class="dropdown-divider"></div>

                                                <div class="dropdown-divider"></div>

                                                @if($provider_subscription_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{ route('admin.provider_subscriptions.status',['provider_subscription_id' => $provider_subscription_details->id] ) }}" onclick="return confirm(&quot;{{$provider_subscription_details->title}}  {{tr('provider_subscription_decline_confirmation')}}&quot;);" >
                                                        {{ tr('decline') }} 
                                                    </a>

                                                @else
                                                    
                                                    <a class="dropdown-item" href="{{ route('admin.provider_subscriptions.status',['provider_subscription_id' => $provider_subscription_details->id] ) }}">
                                                        {{ tr('approve') }} 
                                                    </a>
                                                       
                                                @endif
                                                
                                                <div class="dropdown-divider"></div>


                                                <a class="dropdown-item" href="{{ route('admin.provider_subscriptions.payments',['provider_subscription_id' => $provider_subscription_details->id] )}}"> 
                                                    {{ tr('payments') }}
                                                </a>

                                                </div>

                                            </div>

                                        </div>

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