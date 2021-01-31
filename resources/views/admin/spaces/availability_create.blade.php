@extends('layouts.admin') 

@section('title', tr('view_spaces'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.spaces.index')}}">{{tr('parking_space')}}</a></li>

    <li class="breadcrumb-item"><a href="{{route('admin.spaces.view', ['host_id' => $host_details->id])}}">{{tr('view_spaces')}}</a></li>
  
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('availability')}}</span>
    </li>
           
@endsection

@section('styles')

    <link rel="stylesheet" href="{{asset('admin-assets/css/space.css')}} ">   
    <!-- form time picke css-->
    <link rel="stylesheet" href="{{asset('admin-assets/node_modules/clockpicker/dist/jquery-clockpicker.min.css')}}"/>
    <!-- form checkbox css-->
    <link rel="stylesheet" href="{{asset('admin-assets/node_modules/icheck/skins/all.css')}}"/>

@endsection

@section('content')
			
	<div class="row">
	
		<div class="col-lg-12">
	
			<div class="card">

                <div class="card-header bg-card-header ">


                    <h4 class="text-uppercase"><b>{{tr('availability')}} - <a class="text-white" href="{{ route('admin.spaces.view', ['host_id' => $host_details->id]) }}">{{ $host_details->host_name }}</a></b>

                    <button class="btn btn-secondary pull-right" type="button" onclick="$('#availability_add_form').toggle()"><i class="fa fa-plus"></i> {{tr('add_availability')}}</button>
                        
                    </h4>

                </div>


			  	<div class="card-body" id="availability_add_form" style="display: none">
			     		   
                    <form class="forms-sample" action="{{ Setting::get('is_demo_control_enabled') == NO ? route('admin.spaces.availability.save') : '#'}}" method="POST" enctype="multipart/form-data" role="form">

                    @csrf

                    <div class="card-body">                               
                        
                        <input type="hidden" name="host_id" value="{{ $host_details->id }}">

                        <div class="row">

                            <div class="form-group col-md-4">

                                <label for="service_location_id">{{tr('choose_days')}}</label>

                                <select class="form-control select2" id="available_days" name="available_days" multiple>
                                    <option value="">{{tr('available_days')}}</option>

                                    @foreach($available_days as $week_day)
                                        <option value="{{ $week_day['key'] }}" @if( $week_day['is_selected']) selected @endif>
                                            {{ $week_day['value'] }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="form-group col-md-5">

                                <label for="type">{{tr('status')}}</label>

                                <select class="form-control select2" id="type" name="type">
                                    <option value="1">{{tr('add_space_note')}}</option> 

                                    <option value="0">{{tr('remove_space_note')}}</option>

                                </select>

                            </div>  

                            <div class="form-group col-md-3">

                                <label for="spaces">{{tr('space_to_add_remove')}}</label>

                                <input type="number" class="form-control" id="spaces" name="spaces" value="{{ old('spaces')}}" required>
                            </div>
                        
                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="from_date">{{ tr('from_date') }}</label>

                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='date' class="form-control" id="from_date" name="from_date"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>

                            </div> 

                            
                            <div class="form-group col-md-6">

                                <label for="to_date">{{ tr('to_date') }}</label>

                                <div class='input-group date' id='datetimepicker1'>
                                    <input type='date' class="form-control" id="to_date" name="to_date" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>

                            </div>                            

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="from_time">{{tr('from_time')}}</label>

                                <div class="form-group">
                                    
                                    <div class="input-group mb-2 mr-md-2 mb-md-0">
                                    
                                        <div class="form-group input-group clockpicker">
                                           <input type="time" id="from_time" name="from_time" class="form-control" value="">
                                        </div>

                                    </div>
                                
                                </div>

                            </div>
                            <div class="form-group col-md-6">

                                <label for="to_time">{{tr('to_time')}}</label>

                                <div class="form-group">
                                    
                                    <div class="input-group mb-2 mr-md-2 mb-md-0">
                                    
                                        <div class="form-group input-group clockpicker">
                                           <input type="time" id="to_time" name="to_time" class="form-control" value="">
                                        </div>

                                    </div>
                                
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="reset" class="btn btn-light">{{ tr('reset')}}</button>

                        @if(Setting::get('is_demo_control_enabled') == NO )

                            <button type="submit" class="btn btn-success mr-2">{{ tr('submit') }} </button>

                        @else

                            <button type="button" class="btn btn-success mr-2" disabled>{{ tr('submit') }}</button>
                            
                        @endif

                    </div>

                    </form>

			    </div>

               <!--  <div class="card-header bg-card-header ">

                    <h4 class="text-uppercase"><b>{{tr('availability')}}</b>

                    <button class="btn btn-secondary pull-right" type="button" onclick="$('#availability_add_form').toggle()"><i class="fa fa-plus"></i> {{tr('add_availability')}}</button>
                        
                    </h4>

                </div> -->
                    <hr>
                    <div class="card-body">
                        <h4>{{tr('availabilities')}}</h4>

                        <div class="table-responsive">
                        
                            <table  class="table">
                            
                               @if($hosts_availability_list->count())

                                <thead>
                                  
                                    <tr>
                                        <th>{{ tr('s_no') }}</th>
                                        <th>{{ tr('from_date') }}</th>
                                        <th>{{ tr('to_date') }}</th>
                                        <th>{{ tr('space_count') }}</th>
                                        <th>{{ tr('type') }}</th>
                                        <th>{{ tr('created_at') }}</th>
                                        <th>{{ tr('updated_at') }}</th>
                                    </tr>
                               
                                </thead>                                    

                                <tbody>
                                    
                                    @foreach($hosts_availability_list as $h => $hosts_availability_list_details)
                                    <tr>
                                        <td>{{$h+1}}</td>

                                        <td>{{ common_date($hosts_availability_list_details->from_date) }}</td>
                                      
                                        <td>{{ common_date($hosts_availability_list_details->to_date) }}</td>
                                      
                                        <td>{{ $hosts_availability_list_details->spaces }}</td>

                                        <td>

                                            @if($hosts_availability_list_details->type == HOST_AVAIL_ADD_SPACE)
                                            {{ tr('space_added') }}
                                            @elseif($hosts_availability_list_details->type == HOST_AVAIL_REMOVE_SPACE)
                                            {{ tr('space_removed') }}
                                            @endif

                                        </td>
                                        
                                        <td>{{ common_date($hosts_availability_list_details->created_at) }}</td>
                                        
                                        <td>{{ common_date($hosts_availability_list_details->updated_at) }}</td>
                                        <td>                                    
                                            <a class="btn btn-danger" href="{{ route('admin.spaces.availability.delete', ['host_id'=>$host_details->id, 'hosts_availability_list_id' => $hosts_availability_list_details->id]) }}">
                                                {{tr('delete')}}
                                            </a>                                           
                                        </td>                           

                                    </tr>
                                    @endforeach

                                </tbody>

                                @else
                                    {{tr('no_result_found')}}
                                @endif

                            </table>
                        
                        </div>

                    </div>
        
            </div>

        </div>

   </div>


@endsection

@section('scripts')

<script type="text/javascript">
    $(function () {
        $('#datetimepicker1').datetimepicker();
    });
</script>
        
<!-- form time picke js starts -->
    <script src="{{asset('admin-assets/node_modules/clockpicker/dist/jquery-clockpicker.min.js')}}"></script>
    <script src="{{asset('admin-assets/js/formpickers.js')}}"></script>
<!-- form time picke js ends -->

<script type="text/javascript">

//  @todo saturdy and sunday working enquiery
$(":checkbox").click(function(){
    var id = $(this).attr('id');
});

</script>

@endsection