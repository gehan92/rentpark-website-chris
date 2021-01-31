@extends('layouts.admin') 

@section('title', tr('view_space'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.spaces.index')}}">{{tr('parking_space')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_space')}}</span>
    </li>
           
@endsection

@section('content')

	<div class="row user-profile">
            
		<div class="col-lg-12 side-right stretch-card">
			
			<div class="card">
				
				<div class="card-body">
					
					<div class="wrapper d-block d-sm-flex align-items-center justify-content-between">
						
						<div class="d-lg-flex flex-row text-center text-lg-left">
							<img src="{{ $host->provider_image ?: asset('placeholder.jpg') }}" class="img-sm rounded" alt="image"/>

							<div class="ml-lg-3">
								<p class="text-success font-weight-bold">
									<a href="{{route('admin.providers.view', ['provider_id' => $host->provider_id])}}">{{$host->provider_name}}
									</a>
									<small class="text-muted "><br>{{tr('provider')}}</small>
								</p>
							</div>

						</div>
						
						<ul class="nav nav-tabs tab-solid tab-solid-primary mb-0" id="hostDetails" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-expanded="true" style="padding: 10px;">{{tr('properties')}}</a>
                      		</li>
                      		<li class="nav-item">
                        		<a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab" aria-controls="step2" style="padding: 10px;">{{tr('description')}}</a>
                      		</li>
                      		<li class="nav-item">
                        		<a class="nav-link" id="step3-tab" data-toggle="tab" href="#step3" role="tab" aria-controls="step3" style="padding: 10px;">{{tr('gallery')}}</a>
                      		</li>
                      		<li class="nav-item">
                        		<a class="nav-link" id="step4-tab" data-toggle="tab" href="#step4" role="tab" aria-controls="step4" style="padding: 10px;">{{tr('action')}}</a>
                      		</li>

                      		<li class="nav-item">
                        		<a class="nav-link" id="step6-tab" data-toggle="tab" href="#step6" role="tab" aria-controls="step5" style="padding: 10px;">{{tr('location')}}</a>
                      		</li>
                    	</ul>
                  	</div>
                  	
                  	<div class="wrapper">
                    	<hr>
                    	<div class="tab-content" id="hostDetailsView">

                      		<div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1">

                      			<div class="row">

						            <div class="col-md-6 grid-margin">

						              	<div class="card">

						                	<div class="card-body">
						                  	
						                  		<div class="template-demo">

							                        <table class="table mb-0">

							                            <tbody>
							                            	<tr>
							                                    <td class="pl-0"><b>{{ tr('space_name') }}</b></td>
							                                    <td class="pr-0 text-right">
							                                        <div>{{$host->host_name}}</div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"><b>{{ tr('host_type') }}</b></td>
							                                    <td class="pr-0 text-right">
							                                        <div>{{$host->host_type}}</div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"><b>{{ tr('space_owner_type') }}</b></td>
							                                    <td class="pr-0 text-right">
							                                        <div>{{$host->host_owner_type}}</div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"><b>{{ tr('available_space') }}</b></td>
							                                    <td class="pr-0 text-right">
							                                        <div>{{$host->total_spaces}}</div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"><b>{{ tr('service_location') }}</b></td>
							                                    <td class="pr-0 text-right">
							                                        <div><p class="card-text"><a href="{{route('admin.service_locations.view' , ['service_location_id' => $host->service_location_id] )}}">{{$host->location_name}}</a></p></div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('created_at') }} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div> {{ common_date($host->created_at) }} </div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('updated_at')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ common_date($host->updated_at) }} </div>
							                                    </td>
							                                </tr>

							                            </tbody>

							                        </table>

							                    </div>

						                	</div>

						              	</div>

						            </div>
						           
						            <div class="col-md-6 grid-margin">
							            
							            <div class="card">

							                <div class="card-body">
							                  	
							                  	<div class="template-demo">

							                        <table class="table mb-0">

							                            <tbody>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('pricing_per_hour')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->per_hour}} </div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('pricing_per_day')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->per_day}} </div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('pricing_per_week')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->per_week}} </div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('pricing_per_month')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->per_month}} </div>
							                                    </td>
							                                </tr>
     
							                            </tbody>

							                        </table>
							                    </div>
						                	</div>

										</div>

                      				</div>
						            
                        		</div>
                      		</div><!-- tab content ends -->
                      		
                      		<div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">

								<div class="card-group">
	                        		<div class="card mb-4">
					                    <!-- Card image -->
					                    <div class="view overlay">
					                        <img class="card-img-top" src="{{ $host->picture }}">
					                        <a href="#!">
					                            <div class="mask rgba-white-slight"></div>
					                        </a>
					                    </div>
					                </div>
					                <div class="card mb-4">
					                    <div class="card-body">
						                    <!-- Card content -->
						                    <div class="custom-card">

						                        <!-- Title -->
						                        <h4 class="card-title">{{ tr('description') }}</h4>
						                        <!-- Text -->
						                        <p class="card-text">{{ $host->description }}</p>
						                        
						                    </div>
					                    	<!-- Card content -->
					                   	</div>

					                </div>
					            </div>
                      		</div>

                      		<div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">

								<div class="row grid-margin">

									<div class="col-lg-12">

										<div class="px-3">

					                  		<div id="lightgallery-without-thumb" class="row lightGallery">
					                  			@foreach($host_gallery as $key => $gallery_details) 
							                    <a href="{{ $gallery_details->picture }}" class="image-tile">
							                    	<img src="{{ $gallery_details->picture }}" alt="" style="width: 200px; height: 200px;">
							                    </a>
							                    @endforeach
											</div>

						              	</div>

						            </div>

						        </div>

                      		</div>
                      		<div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">

								<div class="row">

						            <div class="col-md-6 grid-margin">

							            <div class="card">

							                <div class="card-body">
							                  	
							                  	<div class="template-demo">

							                        <table class="table mb-0">

							                            <tbody>
							                            	
							                                <tr>                                
							                                    <td class="pl-0"><b>{{ tr('host_admin_status') }}</b></td>

							                                     <td class="pr-0 text-right">
							                                        @if($host->admin_status == ADMIN_HOST_APPROVED)

							                                        <span class="badge badge-outline-success text-uppercase">{{ tr('ADMIN_HOST_APPROVED') }}</span> 

							                                        @else

							                                        <span class="badge badge-outline-warning text-uppercase">{{ tr('ADMIN_HOST_PENDING') }} </span>

							                                        @endif
							                                    </td>
							                                </tr>

							                                <tr>                                
							                                    <td class="pl-0"><b>{{ tr('host_owner_status') }}</b></td>

							                                     <td class="pr-0 text-right">
							                                        @if($host->status == HOST_OWNER_PUBLISHED)

							                                        <span class="badge badge-success badge-md text-uppercase">{{ tr('HOST_OWNER_PUBLISHED') }}</span> 

							                                        @else

							                                        <span class="badge badge-danger badge-md text-uppercase">{{ tr('HOST_OWNER_UNPUBLISHED') }}</span>

							                                        @endif
							                                    </td>
							                                </tr>

							                                <tr>                                
							                                    <td class="pl-0"><b>{{ tr('verified_status') }}</b></td>

							                                     <td class="pr-0 text-right">
							                                        @if($host->is_admin_verified == ADMIN_HOST_VERIFIED)

							                                        <span class="badge badge-success badge-md text-uppercase">{{ tr('verified') }}</span> 
							                                        @else
							                                        
							                                        <a class="badge badge-info" href="{{ route('admin.spaces.verification_status', ['host_id' => $host->id]) }}">{{ tr('verify') }} </a>

							                                        @endif
							                                    </td>
							                                </tr>

							                            </tbody>

							                        </table>
							                    </div>
						                	</div>

										</div>
                      				</div>
                      				<div class="col-md-6 grid-margin">
							            <div class="card">

							                <div class="card-body">
							                  	
							                  	<div class="template-demo">

							                        <table class="table mb-0">

							                            <tbody>
							                            	<tr>
							                                    <td class="pl-0"> <b> {{ tr('location')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->full_address }} </div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('address')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->street_details.', '. $host->city.','.$host->state.','.$host->zipcode}} </div>
							                                    </td>
							                                </tr>
							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('access_method')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{($host->access_method == ACCESS_METHOD_SECRET_CODE) ? 'Secret Code' : 'Key'}}</div>
							                                    </td>
							                                </tr>

							                                <tr>
							                                    <td class="pl-0"> <b> {{ tr('access_note')}} </b> </td>
							                                    <td class="pr-0  text-right">
							                                        <div>{{ $host->access_note}} </div>
							                                    </td>
							                                </tr>
							                                
							                               
							                            </tbody>

							                        </table>
							                    </div>
						                	</div>

										</div>

                      				</div>

                        		</div>
                        			
                    			<div>
                    				@if(Setting::get('is_demo_control_enabled') == NO)

			                            <a href="{{ route('admin.spaces.edit', ['host_id' => $host->id] ) }}" class="btn btn-primary"><i class="mdi mdi-border-color"></i>{{tr('edit')}}</a>

			                            <a onclick="return confirm(&quot;{{tr('sub_category_delete_confirmation' , $host->name)}}&quot;);" href="{{ route('admin.spaces.delete', ['host_id' => $host->id] ) }}"  class="btn btn-danger"><i class="mdi mdi-delete"></i>{{tr('delete')}}</a>

			                        @else
			                            <a href="javascript:;" class="btn btn-primary"><i class="mdi mdi-border-color"></i>{{tr('edit')}}</a>
			                            
			                            <a href="javascript:;" class="btn btn-danger"><i class="mdi mdi-delete"></i>{{tr('delete')}}</a>

			                        @endif

			                        @if($host->admin_status == APPROVED)

			                            <a class="btn btn-info" href="{{ route('admin.spaces.status', ['host_id' => $host->id] ) }}" onclick="return confirm(&quot;{{$host->host_name}} - {{tr('host_decline_confirmation')}}&quot;);"> <i class="mdi mdi-loop"></i>
			                                {{tr('decline')}}
			                            </a>

			                        @else

			                            <a class="btn btn-success" href="{{ route('admin.spaces.status', ['host_id' => $host->id] ) }}"><i class="mdi mdi-loop"></i>
			                                {{tr('approve')}}
			                            </a>
			                                                   
			                        @endif
				                        
		                    		<a href="{{ route('admin.spaces.availability.create', ['host_id' => $host->id] ) }}" class="btn btn-success"><i class="mdi mdi-message-text"></i>{{tr('availability')}}
		                    		</a>

		                    		<a class="btn btn-primary" href="{{ route('admin.spaces.gallery.index', ['host_id' => $host->id] ) }}">
                                       {{tr('gallery')}}
                                    </a>
			                    		
                    			</div>

                      		</div>
							
							<div class="tab-pane fade" id="step6" role="tabpanel" aria-labelledby="step6-tab">
                
	                      		<div class="col-md-6 col-lg-6 grid-margin">
					              	<div class="card">
						                <div class="card-body">
						                  	<h6 class="card-title">{{ tr('location')}}</h6>
							                <div class="map-container">
							                    <div id="map-with-marker" class="google-map"></div>
							                </div>
						                </div>
					              	</div>
					            </div>
				            
				            </div>

                      	</div>
                  	</div>

                </div>
            
            </div>

		</div>

	</div>

@endsection

@section('scripts')

<script type="text/javascript">
	
function initMap() {
  //Map location
  var MapLocation = {
    lat: {{ $host->latitude }},
    lng: {{ $host->longitude }}
  };

  // Map Zooming
  var MapZoom = 14;

  // Basic Map
  var MapWithMarker = new google.maps.Map(document.getElementById('map-with-marker'), {
    zoom: MapZoom,
    center: MapLocation
  });

  var marker_1 = new google.maps.Marker({
    position: MapLocation,
    map: MapWithMarker
  });

}

</script>
@endsection