@extends('layouts.admin') 

@section('title', tr('host_reviews'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.hosts.index')}}">{{tr('hosts')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('host_reviews')}}</span>
    </li>
           
@endsection

@section('styles')

<link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/star-rating-svg.css')}}">

@endsection

@section('content')

	<div class="row user-profile">
            
		<div class="col-lg-12 side-right stretch-card">
			
			<div class="card">
				
				<div class="card-body">
					
					<div class="wrapper d-block d-sm-flex align-items-center justify-content-between">
						<div class="d-lg-flex flex-row text-center text-lg-left">
							<img src="{{ $host->picture ?: asset('placeholder.jpg') }}" class="img-sm rounded" alt="image"/>
							<div class="ml-lg-3">
								<p class="mt-2 text-success font-weight-bold">
									<a href="{{route('admin.hosts.view', ['host_id' => $host->id])}}">{{$host->host_name}}
									</a>
								</p>
							</div>
						</div>
						
						<ul class="nav nav-tabs tab-solid tab-solid-primary mb-0" id="hostDetails" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-expanded="true" style="padding: 10px;">{{tr('user_reviews')}}</a>
                      		</li>
                      		<li class="nav-item">
                        		<a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab" aria-controls="step2" style="padding: 10px;">{{tr('provider_reviews')}}</a>
                      		</li>
                    	</ul>
                  	</div>
                  	
                  	<div class="wrapper">
                    	<hr>
                    	<div class="tab-content" id="hostDetailsView">

                      		<div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1">
                      			@if(count($user_reviews) > 0)
                      			<div class="card">
									
									<div class="card-body">
						             	
						                <div class="row">
				                    		<div class="col-12">
				                    			@foreach($user_reviews as $key=>$reviews)
				                      			<div class="wrapper border-bottom py-2">
				                        			<div class="d-flex">
				                          				<img class="img-sm rounded-circle" src="{{ $reviews->userDetails->picture ?: asset('placeholder.jpg') }}" alt="{{$reviews->userDetails->name}}">
						                          		<div class="wrapper ml-4">
						                            		<p class="mb-0">
						                            			<a href="{{route('admin.users.view', ['user_id' => $reviews->userDetails->id])}}">{{$reviews->userDetails->name}}</a>
						                            		</p>
						                            		<small class="text-muted mb-0">{{$reviews->review}}</small>
						                          		</div>
						                          		<div class="rating ml-auto d-flex align-items-center">
						                          			<div class="user-rating-{{$key}}"></div>
						                          		</div>
													</div>
												</div>
												@endforeach
											</div>
										</div>
									</div>    		
								</div>
								@else
								<h5>{{tr('no_reviews_found')}}</h5>
								@endif
                      		</div>
                      	
                      		<!-- tab content ends -->
                      		
                      		<div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                      			@if(count($provider_reviews) > 0)
                      			<div class="card">
									
									<div class="card-body">
						             	
						                <div class="row">
				                    		<div class="col-12">
				                    			@foreach($provider_reviews as $i=>$reviews)
				                      			<div class="wrapper border-bottom py-2">
				                        			<div class="d-flex">
				                          				<img class="img-sm rounded-circle" src="{{ $reviews->providerDetails->picture ?: asset('placeholder.jpg') }}" alt="{{$reviews->providerDetails->name}}">
						                          		<div class="wrapper ml-4">
						                            		<p class="mb-0">
						                            			<a href="{{route('admin.providers.view', ['provider_id' => $reviews->providerDetails->id])}}">{{$reviews->providerDetails->name}}</a>
						                            		</p>
						                            		<small class="text-muted mb-0">{{$reviews->review}}</small>
						                          		</div>
						                          		<div class="rating ml-auto d-flex align-items-center">
						                          			<div class="provider-rating-{{$i}}"></div>
						                          		</div>
													</div>
												</div>
												@endforeach
											</div>
										</div>
									</div>    		
								</div>
								@else
								<h5>{{tr('no_reviews_found')}}</h5>
								@endif
                      		</div>

                    	</div>

                  	</div>

                </div>
            
            </div>

		</div>

	</div>

@endsection

@section('scripts')

     <script type="text/javascript" src="{{asset('admin-assets/js/jquery.star-rating-svg.min.js')}}"> </script>

    <script>
        <?php foreach ($user_reviews as $i => $details) { ?>
            $(".user-rating-{{$i}}").starRating({
                starSize: 25,
                initialRating: "{{$details->ratings}}",
                readOnly: true,
                callback: function(currentRating, $el){
                    // make a server call here
                }
            });
        <?php } ?>

        <?php foreach ($provider_reviews as $i => $details) { ?>
            $(".provider-rating-{{$i}}").starRating({
                starSize: 25,
                initialRating: "{{$details->ratings}}",
                readOnly: true,
                callback: function(currentRating, $el){
                    // make a server call here
                }
            });
        <?php } ?>
    </script>

@endsection