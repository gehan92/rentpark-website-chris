@extends('layouts.admin') 

@section('title', tr('view_spaces'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.hosts.index')}}">{{tr('parking_space')}}</a></li>

    <li class="breadcrumb-item">

        <a href="{{route('admin.hosts.view' , ['host_id' => $host->id])}}">{{tr('view_host')}}</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('gallery')}}</span>
    </li>
           
@endsection

@section('content')
	
	
        <div class="row">
            <div class="col-md-6 d-flex align-items-stretch grid-margin">
            	<div class="row flex-grow">
                	<div class="col-12 grid-margin">
                  		<div class="card">
                    		<div class="card-body">
			                    <p class="card-description">
			                        {{tr('add_images')}}
			                    </p>
                      			<form class="forms-sample" action="{{ route('admin.hosts.gallery.save') }}" method="POST" enctype="multipart/form-data" role="form">
                      				@csrf

                      				<input type="hidden" name="host_id" id="host_id" value="{{$host->id}}">

			                        <div class="form-group">

                                    	<label>{{tr('gallery')}}</label>

                                    	<input type="file" class="form-control" required name="pictures[]" multiple accept="image/*" placeholder="{{tr('upload_image')}}">

                                	</div>
			                        <button type="submit" class="btn btn-success mr-2">Add</button>
                      			</form>
                    		</div>
                  		</div>
                	</div>
                </div>
            </div>
        </div>
    
	<div class="row user-profile">
            
		<div class="col-lg-12 side-right stretch-card">
			
			<div class="card">
			 	
			 	<div class="card-header bg-card-header ">

		            <h4 class="text-uppercase"><b>{{tr('gallery')}} - <a href="{{route('admin.hosts.view' , ['host_id' => $host->id])}}">{{ $host->host_name }}</a> </b>

		            </h4>

        		</div>
				
				<div class="card-body">
					
					<div class="row grid-margin">

						@foreach($hosts_galleries as $key => $gallery)

							<div class="col-sm-3">
								
								<img src="{{ $gallery->picture }}" alt="" style="width: 200px; height: 200px;">
								<br>										
								<a class="btn btn-outline-primary" style="margin : 10px;" href="{{ route('admin.hosts.gallery.delete', ['gallery_id' => $gallery->id]) }}" class="btn btn-primary" onclick="return confirm(&quot;{{tr('gallery_delete_confirmation')}}&quot;);" title="{{ tr('delete')}}" >
								<i class="fa fa-trash-o"></i>
								</a>

							</div>
						
						@endforeach

					</div>
				
				</div>
            
            </div>

		</div>

	</div>

@endsection