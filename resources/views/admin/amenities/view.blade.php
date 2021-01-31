@extends('layouts.admin') 

@section('title', tr('view_amenity'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.amenities.index')}}">{{tr('amenities')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_amenity')}}</span>
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
                    <div class="view overlay">
                        <img class="card-img-top" src="{{ $amenity_details->picture ?: asset('amenities-placeholder.png') }}">
                        
                    </div>
                    <!-- Card content -->

                </div>
                <!-- Card -->

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('sapce_type')}}</h5>
                            
                            <p class="card-text">{{$amenity_details->type}}</p>

                        </div>  

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('name')}}</h5>
                            
                            <p class="card-text">{{$amenity_details->value}}</p>

                        </div> 

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('status')}}</h5>
                            
                            <p class="card-text">

                                @if($amenity_details->status == APPROVED)

                                    <span class="badge badge-success badge-md text-uppercase">{{tr('approved')}}</span>

                                @else 

                                    <span class="badge badge-danger badge-md text-uppercase">{{tr('pending')}}</span>

                                @endif
                            
                            </p>

                        </div>
                                                
                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('updated_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($amenity_details->updated_at) }}</p>

                        </div>

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('created_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($amenity_details->created_at) }}</p>

                        </div> 

                    </div>
                    <!-- Card content -->

                </div>

                <!-- Card -->

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        @if(Setting::get('is_demo_control_enabled') == NO )

                            <a href="{{ route('admin.amenities.edit',['amenity_id' => $amenity_details->id] ) }}" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                            <a onclick="return confirm(&quot;{{tr('amenity_delete_confirmation' , $amenity_details->value)}}&quot;);" href="{{ route('admin.amenities.delete',['amenity_id' => $amenity_details->id] ) }}"  class="btn btn-danger btn-block">{{tr('delete')}}</a>

                        @else
                            <a href="javascript:;" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                            <a href="javascript:;" class="btn btn-danger btn-block">{{tr('delete')}}</a>

                        @endif

                        @if($amenity_details->status == APPROVED)

                            <a class="btn btn-danger btn-block" href="{{ route('admin.amenities.status',['amenity_id' => $amenity_details->id] ) }}" 
                            onclick="return confirm(&quot;{{$amenity_details->value}} - {{tr('amenity_decline_confirmation')}}&quot;);"> 
                                {{tr('decline')}}
                            </a>

                        @else

                            <a class="btn btn-success btn-block" href="{{ route('admin.amenities.status',['amenity_id' => $amenity_details->id] ) }}">
                                {{tr('approve')}}
                            </a>
                                                   
                        @endif

                    </div>
                    <!-- Card content -->

                </div>
                <!-- Card -->

            </div>
            <!-- Card group -->

        </div>

    </div>

@endsection