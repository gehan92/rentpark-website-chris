@extends('layouts.admin') 

@section('title', tr('view_provider_subscription'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.provider_subscriptions.index')}}">{{tr('provider_subscriptions')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_provider_subscription')}}</span>
    </li>
           
@endsection  

@section('content')
    
    <div class="row">

        <div class="col-md-12">

            <!-- Card group -->
            <div class="card-group">

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card image -->
                   
                    <div class="card-body">

                        <div class="row">

                            <h5 class="col-md-12">{{tr('title')}}</h5>

                            <p class="col-md-12 text-muted">{{$provider_subscription_details->title}}</p>

                            @if($provider_subscription_details->description)

                                <h5 class="col-md-12">{{tr('description')}}</h5>

                                <p class="col-md-12 text-muted">{{$provider_subscription_details->description}}</p>
                            @endif
                            
                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-6">

                                @if(Setting::get('is_demo_control_enabled') == YES)

                                    <a href="javascript:;" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                                    <a href="javascript:;" class="btn btn-danger btn-block">{{tr('delete')}}</a>

                                @else

                                    <a class="btn btn-primary btn-block" href="{{ route('admin.provider_subscriptions.edit',['provider_subscription_id' => $provider_subscription_details->id]) }}">{{tr('edit')}}</a>

                                    <a class="btn btn-danger btn-block" href="{{route('admin.provider_subscriptions.delete',['provider_subscription_id' => $provider_subscription_details->id] )}}" onclick="return confirm(&quot;{{tr('provider_subscription_delete_confirmation' , $provider_subscription_details->title)}}&quot;);">{{tr('delete')}}</a>

                                @endif

                            </div>
                            
                            <div class="col-md-6">

                                @if($provider_subscription_details->status == APPROVED)

                                    <a class="btn btn-danger btn-block" href="{{ route('admin.provider_subscriptions.status', ['provider_subscription_id' => $provider_subscription_details->id]) }}" onclick="return confirm(&quot;{{$provider_subscription_details->title}} {{tr('provider_subscription_decline_confirmation')}}&quot;);" >
                                        {{ tr('decline') }} 
                                    </a>

                                @else
                                    
                                    <a class="btn btn-success btn-block" href="{{ route('admin.provider_subscriptions.status', ['provider_subscription_id' => $provider_subscription_details->id] ) }}">
                                        {{ tr('approve') }} 
                                    </a>
                                       
                                @endif

                            </div>

                        </div>

                    
                    </div>

                </div>
                <!-- Card -->

                <!-- Card -->
                <div class="card mb-8">

                    <!-- Card content -->
                    <div class="card-body">

                        <div class="template-demo">

                            <table class="table mb-0">

                                <tbody>

                                    <tr>
                                        <td class="pl-0"><b>{{ tr('title') }}</b></td>
                                        <td class="pr-0 text-right"><div >{{$provider_subscription_details->title}}</div></td>
                                    </tr> 

                                    <tr>
                                        <td class="pl-0"><b>{{ tr('amount') }}</b></td>
                                        <td class="pr-0 text-right"><div >{{formatted_amount($provider_subscription_details->amount)}}</div></td>
                                    </tr> 

                                    <tr>
                                        <td class="pl-0"><b>{{ tr('plan') }}</b></td>
                                        <td class="pr-0 text-right"><div >{{$provider_subscription_details->plan}}</div></td>
                                    </tr> 

                                    <tr>
                                        <td class="pl-0"><b>{{ tr('plan_type') }}</b></td>
                                        <td class="pr-0 text-right"><div >{{$provider_subscription_details->plan_type}}</div></td>
                                    </tr> 

                                    <tr style="display: none;"> 
                                        <td class="pl-0"><b>{{ tr('is_popular') }}</b></td>
                                        
                                        <td class="pr-0 text-right">
                                            @if($provider_subscription_details->is_popular == YES)
                                                <span class="card-text badge badge-success badge-md text-uppercase">{{tr('yes')}}</span>
                                            @else
                                                <span class="card-text  badge badge-danger badge-md text-uppercase">{{tr('no')}}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pl-0"><b>{{ tr('total_subscribers') }}</b></td>
                                        <td class="pr-0 text-right">
                                            <div>
                                                <a href="{{ route('admin.provider_subscriptions.payments' ,['provider_subscription_id' => $provider_subscription_details->id])}}" class="btn btn-success btn-xs">

                                                    {{ $provider_subscription_details->total_subscriptions }} 
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pl-0"><b>{{ tr('revenue') }}</b></td>
                                        <td class="pr-0 text-right">
                                            <div>
                                                {{ formatted_amount($provider_subscription_details->total_revenue) }} 
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pl-0"> <b>{{ tr('created_at') }}</b></td>
                                        <td class="pr-0 text-right"><div>{{common_date($provider_subscription_details->created_at, Auth::check() ? Auth::guard('admin')->user()->timezone : 0)}}</div></td>
                                    </tr>

                                    <tr>
                                        <td class="pl-0"> <b>{{ tr('updated_at') }}</b></td>
                                        <td class="pr-0 text-right"><div>{{common_date($provider_subscription_details->updated_at, Auth::check() ? Auth::guard('admin')->user()->timezone : 0)}}</div></td>
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