@extends('layouts.admin') 

@section('title', tr('view_wishlist'))

@section('breadcrumb')

	<li class="breadcrumb-item"><a href="{{ route('admin.users.index')}}">{{tr('users')}}</a></li>

	<li class="breadcrumb-item" aria-current="page">
	    <a href="{{ route('admin.users.index')}}">{{ tr('view_users') }}</a>
	</li> 
    
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('wishlist')}}</span>
    </li> 
           
@endsection 

@section('content')

	<div class="col-lg-12 grid-margin stretch-card">
	    
	    <div class="card">

	        <div class="card-header bg-card-header ">

	            <h4 class="">

	            	{{tr('wishlist')}} - 
	            	
	            	<a href="{{route('admin.users.view',['user_id' => $user_details->id ])}}" class="text-white">
	            		{{ $user_details->name}} 
	            	</a>

	                <a class="btn btn-secondary pull-right" href="{{route('admin.users.view',['user_id' => $user_details->id ])}}">
	                	<i class="fa fa-eye"></i> 
	                	{{ tr('view_user') }}
	                </a>

	            </h4>

	        </div>

	        <div class="card-body">

	            <div class="table-responsive">

            	    <table id="order-listing" class="table">
	            	       
            	        <thead>
            	       
            	            <tr>
            	                <th>{{ tr('s_no') }}</th>
            	                <th>{{ tr('host') }}</th>
            	                <th>{{ tr('action') }}</th>
            	            </tr>
            	       
            	        </thead>
            	      
            	        <tbody>

            	            @foreach($wishlists as $i => $wishlist_details)
            	            	
            	            	<tr>
	            	            	
	            	            	<td>{{$i+1}}</td>

	                                <td>
	                                    <a href="{{route('admin.spaces.view' , ['host_id' => $wishlist_details->host_id])}}"> {{ $wishlist_details->hostDetails->host_name ?? tr('host_not_avail') }}
	                                    </a>
	                                </td>
	                              
	                                <td>

                                        @if(Setting::get('is_demo_control_enabled') == NO)

                                            <a class="btn btn-danger" href="{{route('admin.wishlists.delete', ['wishlist_id' => $wishlist_details->id])}}" 
                                            onclick="return confirm(&quot;{{tr('wishlist_delete_confirmation' , $wishlist_details->hostDetails->host_name)}}&quot;);">
                                                {{tr('remove')}}
                                            </a>
                                        @else
                                          
                                            <a class="btn btn-danger" href="javascript:;">{{tr('remove')}}</a> 

                                        @endif
                                                      
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