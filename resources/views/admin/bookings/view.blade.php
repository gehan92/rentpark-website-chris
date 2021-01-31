@extends('layouts.admin') 

@section('title', tr('view_booking'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.bookings.index')}}">{{tr('bookings')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_booking')}}</span>
    </li>
           
@endsection  

@section('content')
	
	<div class="row ">
		
		<div class="col-12">
          	
          	<div class="card grid-margin ">
          		
          		<div class="card-body">

          		  	<div class="d-flex justify-content-between align-items-center">
	          		    <div class="d-inline-block">
	          		      	<div class="d-lg-flex">
		          		        <h5 class="mb-2 text-uppercase">
		          		        	<b>
		          		        		{{ tr('booking_id')}}: 
		          		        		<span class="text-success">#{{$booking_details->unique_id}}</span>
		          		        	</b>
		          		        </h5>
	          		      	</div>

	          		        <p>

          		          		<i class="mdi mdi-clock text-muted"></i>

	          		        	{{common_date($booking_details->checkin, Auth::guard('admin')->user()->timezone, 'd M Y H:i')}}
	          		        	-
	          		        	{{common_date($booking_details->checkout, Auth::guard('admin')->user()->timezone, 'd M Y H:i')}}

	          		        </p>

	          		    </div>

	          		    <div class="d-inline-block">
	          		      	<div class="px-3 px-md-4 py-2 rounded text-uppercase text-success">
		          		        <b>{{ booking_status( $booking_details->status) }}</b>
	          		      	</div>
	          		    </div>

          		  	</div>

          		</div>

          	</div>

        </div>

	</div>

    <div class="row">
        
        <div class="col-12 grid-margin">
          	
          	<div class="card">
	            
	            <div class="card-body">
	              	
	              	<div class="row">

		                <div class="col-md-4 col-sm-6 d-flex justify-content-center border-right">
		                 	<div class="wrapper text-center">
		                    	<h4 class="card-title">{{ tr('user') }}</h4>
		                        <img src="{{ $booking_details->user_picture}}" alt="image" class="img-lg rounded-circle mb-2"/>
		 		                <h4>{{ $booking_details->user_name }}</h4>

		 		                <a href="{{ route('admin.users.view', ['user_id' => $booking_details->user_id ]) }}" class="btn btn-outline-success" >{{ tr('view')}}</a>

		 		        	</div>

		                </div>

		                <div class="col-md-4 col-sm-6 d-flex justify-content-center border-right">
		                  <div class="wrapper text-center">
		                    <h4 class="card-title">{{ tr('parking_space')}}</h4>
		                    <img src="{{ $booking_details->host_picture}}" alt="image" class="img-lg rounded-circle mb-2"/>

		                    <p class="card-description">{{ $booking_details->host_name }}</p>
		                    <a href="{{ route('admin.spaces.view', ['host_id' => $booking_details->host_id ]) }}" class="btn btn-outline-success">{{ tr('view')}}</a>

		                  </div>
		                </div>

		                <div class="col-md-4 col-sm-6 d-flex justify-content-center">
		                  <div class="wrapper text-center">
		                    <h4 class="card-title">{{ tr('provider')}}</h4>
		                    <img src="{{ $booking_details->provider_picture}}" alt="image" class="img-lg rounded-circle mb-2"/>

		                    <p class="card-description">{{ $booking_details->provider_name }}</p>
		                    <a href="{{ route('admin.providers.view', ['provider_id' => $booking_details->provider_id ]) }}" class="btn btn-outline-success">{{ tr('view')}}</a>

		                  </div>
		                </div>

	              	</div>

	            </div>

          	</div>

        </div>

    </div>


	<div class="row">
	
		<div class="col-md-4 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
			      	<div class="template-demo">
				        <table class="table mb-0">
				            <tbody>


			            	<tr>
			            	  <td class="pl-0">{{ tr('user_vehicle_id') }}</td>
			            	  <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-danger">{{ $booking_details->user_vehicle_id }}</div></td>
			            	</tr>

			            	<tr>
			            	  <td class="pl-0">{{ tr('duration') }}</td>
			            	  <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-danger">{{ $booking_details->duration }}</div></td>
			            	</tr>

				            <tr>
				              <td class="pl-0">{{ tr('total_days') }}</td>
				              <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-primary">{{ $booking_details->total_days }}</div></td>
				            </tr> 
				            <tr>
				              <td class="pl-0">{{ tr('per_hour') }}</td>
				              <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-primary">{{ $booking_details->per_hour }}</div></td>
				            </tr>

				            <tr>
				              <td class="pl-0">{{ tr('per_day') }}</td>
				              <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-primary">{{ $booking_details->per_day }}</div></td>
				            </tr>
				            <tr>
				              <td class="pl-0">{{ tr('per_week') }}</td>
				              <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-info">{{ $booking_details->per_week }}</div></td>
				            </tr>
				            <tr>
				              <td class="pl-0">{{ tr('per_month') }}</td>
				              <td class="pr-0 text-right"><div class="badge badge-pill badge-outline-success">{{ $booking_details->per_month }}</div></td>
				            </tr>
				          </tbody>
				        </table>
			      	</div>
				</div>
			</div>
		</div>

		<div class="col-md-8 grid-margin stretch-card">
			<div class="card">
				<div class="card-body">
					<div class="preview-list">
						
						<div class="preview-item border-bottom px-0">
							
							<div class="preview-item-content d-flex flex-grow">
								<div class="flex-grow">
									<h6 class="preview-subject">{{tr('total')}}
										<span class="float-right small">
											<span class="text-muted pr-3">{{ formatted_amount($booking_details->total) }}</span>
										</span>
									</h6>
									<!-- <p>Meeting is postponed</p> -->
								</div>
							</div>
						</div>

						<div class="preview-item border-bottom px-0">
							
							<div class="preview-item-content d-flex flex-grow">
								<div class="flex-grow">
									<h6 class="preview-subject">{{tr('payment_mode')}}
										<span class="float-right small">
											<span class="text-muted pr-3">{{ $booking_details->payment_mode }}</span>
										</span>
									</h6>
									<!-- <p>Please approve the request</p> -->
								</div>
							</div>
						</div>

						<div class="preview-item border-bottom px-0">
							
							<div class="preview-item-content d-flex flex-grow">
								<div class="flex-grow">
									<h6 class="preview-subject">{{tr('admin_amount')}}
										<span class="float-right small">
											<span class="text-muted pr-3">{{ formatted_amount($booking_payment_details->admin_amount) }}</span>
										</span>
									</h6>
									<!-- <p>Meeting is postponed</p> -->
								</div>
							</div>
						</div>

						<div class="preview-item border-bottom px-0">
							
							<div class="preview-item-content d-flex flex-grow">
								<div class="flex-grow">
									<h6 class="preview-subject">{{tr('provider_amount')}}
										<span class="float-right small">
											<span class="text-muted pr-3">{{ formatted_amount($booking_payment_details->provider_amount) }}</span>
										</span>
									</h6>
									<!-- <p>Meeting is postponed</p> -->
								</div>
							</div>
						</div>

						<div class="preview-item border-bottom px-0">
							
							<div class="preview-item-content d-flex flex-grow">
								<div class="flex-grow">
									<h6 class="preview-subject">{{tr('updated_at')}}
										<span class="float-right small">
											<span class="text-muted pr-3">{{ common_date($booking_details->updated_at) }}</span>
										</span>
									</h6>
									<!-- <p>Hope to see you tomorrow</p> -->
								</div>
							</div>
						</div>	

						<div class="preview-item border-bottom px-0">
							
							<div class="preview-item-content d-flex flex-grow">
								<div class="flex-grow">
									<h6 class="preview-subject">{{tr('created_at')}}
										<span class="float-right small">
											<span class="text-muted pr-3">{{ common_date($booking_details->created_at) }}</span>
										</span>
									</h6>
									<!-- <p>Hope to see you tomorrow</p> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

 	<!-- booking payments details begins -->

 	@if($booking_payment_details) 
		
		<div class="row">
			
			<div class="col-md-12 grid-margin stretch-card">
			
			  	<div class="card">
			
				    <div class="card-body">
				      	
				      	<h4 class="card-title">{{tr('payment_details')}}</h4>

				      	<div class="row">
			                
			                <div class="col-md-6 col-sm-6 d-flex justify-content-center border-right">  	
					        	<table class="table mb-0">
						          						          	
						          	<tbody>
							            <tr>
							              <td class="pl-0">{{ tr('payment_id') }}</td>
							              <td class="pr-0 text-right"><div class="badge badge-outline-primary">#{{ $booking_payment_details->payment_id}}</div></td>
							            </tr>

							            <tr>
							              <td class="pl-0">{{ tr('payment_mode') }}</td>
							              <td class="pr-0 text-right"><div class="badge badge-outline-info">{{ $booking_payment_details->payment_mode}}</div></td>
							            </tr>

							            <tr>
							              	<td class="pl-0">{{ tr('total_time') }}</td>
							              	<td class="pr-0 text-right">
								              	<div class="badge badge-outline-danger">
								              		{{$booking_payment_details->total_time}} {{tr('per_day_text')}}
								              	</div>
							              	</td>
							            </tr> 


							            <tr>
							              	<td class="pl-0">{{tr('time_price')}}</td>
							              	<td class="pr-0 text-right">
							              		<div class="badge badge-outline-warning">
							              			{{formatted_amount($booking_payment_details->time_price)}}
							              		</div>
							              	</td>
							            </tr>

							            <tr>
							              	<td class="pl-0">{{ tr('base_price') }}</td>
							              	<td class="pr-0 text-right">
								              	<div class="badge badge-outline-danger">
								              		{{$booking_payment_details->base_price}}
								              	</div>
							              	</td>
							            </tr>

							            <tr>
							              	<td class="pl-0">{{ tr('per_day') }}</td>
							              	<td class="pr-0 text-right">
							              		<div class="badge badge-outline-success">
							              			{{formatted_amount($booking_payment_details->per_day)}}
							              		</div>
							              	</td>
							            </tr>

							            <tr>
				                          	<td class="pl-0">{{tr('cleaning_fee')}}</td>

				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{ formatted_amount($booking_payment_details->cleaning_fee) }}
				                          		</div>
				                          	</td>
				                        </tr> 
				                        <tr>
				                          	<td class="pl-0">{{tr('other_price')}}</td>
				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{ formatted_amount($booking_payment_details->other_price) }}
				                          		</div>
				                          	</td>
				                        </tr> 

				                        
				                       
						          	</tbody>

					        	</table>

			                </div>
 							
 							<div class="col-md-6 col-sm-6 d-flex justify-content-center border-right">	
			      	        	<table class="table mb-0">
			      		          						          	
			      		          	<tbody>
			      		          		

			      		          		<tr>
			      		          		  	<td class="pl-0">{{tr('paid_date')}}</td>

			      		          		  	<td class="pr-0 text-right">
			      		          		  		<div class="badge badge-pill badge-outline-primary">
			      		          		  			{{common_date($booking_payment_details->paid_date)}}
			      		          		  		</div>
			      		          		  	</td>
			      		          		</tr>
			      		          		<tr>
				                          	<td class="pl-0">{{tr('tax_price')}}</td>
				                          	<td class="pr-0 text-right">
					                          	<div class="badge badge-pill badge-outline-primary">
					                          		{{ formatted_amount($booking_payment_details->tax_price) }}
					                          	</div>
				                          	</td>
				                        </tr>
				                        <tr>
				                          	<td class="pl-0">{{tr('sub_total')}}</td>
				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{ formatted_amount($booking_payment_details->sub_total)}}
				                          		</div>
				                          	</td>
				                        </tr> 
				                       
				                        <tr>
				                          	<td class="pl-0">{{tr('actual_total')}}</td>
				                          	<td class="pr-0 text-right">
					                          	<div class="badge badge-pill badge-outline-primary">
					                          	
					                          		{{formatted_amount($booking_payment_details->actual_total)}}
					                          	</div>
				                          	</td>
				                        </tr>

			      			            <tr>
				                          	<td class="pl-0">{{tr('total')}}</td>
				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{formatted_amount($booking_payment_details->total)}}
				                          		</div>
				                          	</td>
				                        </tr> 

				                        <tr>
				                          	<td class="pl-0">{{tr('paid_amount')}}</td>
				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{formatted_amount($booking_payment_details->paid_amount)}}
				                          		</div>
				                          	</td>
				                        </tr>
				                        <tr>
				                          	<td class="pl-0">{{tr('admin_amount')}}</td>
				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{formatted_amount($booking_payment_details->admin_amount)}}
				                          		</div>
				                          	</td>
				                        </tr> 

				                        <tr>
				                          	<td class="pl-0">{{tr('provider_amount')}}</td>
				                          	<td class="pr-0 text-right">
				                          		<div class="badge badge-pill badge-outline-primary">
				                          			{{formatted_amount($booking_payment_details->provider_amount)}}
				                          		</div>
				                          	</td>
				                        </tr>
			      			        </tbody>
			      			    
			      			    </table>
					      	</div>

		                </div>

				    </div>
			  	
			  	</div>
			
			</div>

		</div>
	@endif

	<!-- booking payments details begins -->

@endsection