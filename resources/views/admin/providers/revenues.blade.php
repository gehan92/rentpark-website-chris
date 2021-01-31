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
            <div class="card mb-6" style="display: none;">

                <!-- Card image -->
                <div class="view overlay">
                    <img class="card-img-top" src="{{$provider_details->picture}}">
                    <a href="#!">
                        <div class="mask rgba-white-slight"></div>
                    </a>
                </div>

                <!-- Card content -->
                <div class="card-body">

                    <!-- Title -->
                    <h4 class="card-title">{{tr('description')}}</h4>
                    <!-- Text -->
                    <p class="card-text">{{$provider_details->description}}</p>

                </div>
                <!-- Card content -->

            </div>
            <!-- Card -->

            <!-- Card -->
            <div class="card mb-6">

                <div class="card-header">

                    <h3>{{tr('revenues')}} - <a href="{{route('admin.providers.view' , ['provider_id' => $provider_details->id])}}">{{ $provider_details->name }}</a></h3>

                </div>

                <!-- Card content -->
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-4">

                            <div class="card general-box general-box-info">
                                <div class="card-body">
                                    <a href="javascript:void(0);" target="_blank" class="a-tag">
                                        <div class="d-flex align-items-center justify-content-md-center">
                                            <i class="fa fa-money icon-lg text-white"></i>
                                            <div class="ml-3">
                                                <h4>{{ tr('total') }}</h4>
                                                <h6>{{formatted_amount($provider_details->total_provider_amount)}}</h6>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        
                        </div>

                        <div class="col-md-4">

                            <div class="card general-box general-box-warning">
                                <div class="card-body">
                                    <a href="javascript:void(0);" target="_blank" class="a-tag">
                                        <div class="d-flex align-items-center justify-content-md-center">
                                            <i class="fa fa-money icon-lg text-white"></i>
                                            <div class="ml-3">
                                                <h4>{{date('M')}} {{ tr('month') }}</h4>
                                                <h6>{{formatted_amount($provider_details->month_provider_amount)}}</h6>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        
                        </div>

                        <div class="col-md-4">

                            <div class="card general-box general-box-success">
                                <div class="card-body">
                                    <a href="javascript:void(0);" target="_blank" class="a-tag">
                                        <div class="d-flex align-items-center justify-content-md-center">
                                            <i class="fa fa-money icon-lg text-white"></i>
                                            <div class="ml-3">
                                                <h4>{{ tr('today') }}</h4>
                                                <h6>{{formatted_amount($provider_details->today_provider_amount)}}</h6>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                       
                        </div>
                    
                    </div>

                    <hr>

                    <div>
                        <h2>{{tr('provider_revenue_details')}}</h2>

                        <hr>
                        <?php $i = 1 ?>
                        <div class="table-responsive">
                   
                            <table id="order-listing" class="table">
                            
                                <thead>
                                    <tr>
                                        <th>{{tr('s_no')}}</th>
                                        <th>{{tr('host_name')}}</th>
                                        <th>{{tr('total_revenue')}}</th>
                                        <th>{{tr('admin_earnings')}}</th>
                                        <th>{{tr('provider_earnings')}}</th>
                                    </tr>
                                </thead>
                            
                                <tbody>

                                    @if(count($hosts)>0)
                                        @foreach($hosts as $i => $host)
                                   
                                            <tr>
                                                    <td>{{$i+1}}</td>

                                                    <td>{{$host->host_name}}</td>

                                                    <td>{{formatted_amount($host->total_earnings)}}</td>

                                                    <td>{{formatted_amount($host->admin_earnings)}}</td>

                                                    <td>{{formatted_amount($host->provider_earnings)}}</td>

                                            </tr>
                                            
                                        @endforeach 
                                    @else                             
                                        <h3>{{ tr('no_result_found') }}</h3>

                                    @endif
                                                                         
                                </tbody>
          
                            </table>
          
                        </div>

                    </div>

                    

                </div>
                <!-- Card content -->

            </div>

            <!-- Card -->
        </div>
        <!-- Card group -->

    </div>

</div>
@endsection