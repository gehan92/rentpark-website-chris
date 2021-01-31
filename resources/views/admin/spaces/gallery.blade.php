@extends('layouts.admin') 

@section('title', tr('view_spaces'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.spaces.index')}}">{{tr('parking_space')}}</a></li>

    <li class="breadcrumb-item">
        <a href="{{route('admin.spaces.view' , ['host_id' => $host_details->id])}}">{{tr('view_spaces')}}</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('gallery')}}</span>
    </li>
           
@endsection

@section('content')
	
	<div class="row user-profile">
            
		<div class="col-lg-12 side-right stretch-card">
			
			<div class="card">
			 	
			 	<div class="card-header bg-card-header ">

		            <h4 class="text-uppercase"><b>{{tr('gallery')}} - <a class="text-white" href="{{route('admin.spaces.view' , ['host_id' => $host_details->id])}}">{{ $host_details->host_name }}</a> </b>
		            </h4>

        		</div>
				
				<div class="card-body">
					
					<div class="row grid-margin">

						@foreach($hosts_galleries as $key => $gallery)

							<div class="col-sm-3">
								
								<img src="{{ $gallery->picture }}" alt="" style="width: 200px; height: 200px;">
								<br>										
								<a class="btn btn-outline-primary" style="margin : 10px;" href="{{ route('admin.spaces.gallery.delete', ['gallery_id' => $gallery->id]) }}" class="btn btn-primary" onclick="return confirm(&quot;{{tr('gallery_delete_confirmation')}}&quot;);" title="{{ tr('delete')}}" >
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