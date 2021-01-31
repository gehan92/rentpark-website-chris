@extends('layouts.admin') 

@section('title', tr('view_amenities'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.amenities.index') }}">{{tr('amenities')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_amenities') }}</span>
    </li>
           
@endsection 

@section('content')

    <div class="col-lg-12 grid-margin stretch-card">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('view_amenities')}}

                    <a class="btn btn-secondary pull-right" href="{{route('admin.amenities.create')}}">
                        <i class="fa fa-plus"></i> {{tr('add_amenity')}}
                    </a>
                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
                    
                    <table id="order-listing" class="table">
                       
                        <thead>
                            <tr>
                                <th>{{tr('s_no')}}</th>
                                <th>{{tr('picture') }}</th>
                                <th>{{tr('sapce_type')}}</th>
                                <th>{{tr('name')}}</th>
                                <th>{{tr('status')}}</th>
                                <th>{{tr('action')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                         
                            @foreach($amenities as $i => $amenity_details)

                                <tr>
                                    <td>{{$i+1}}</td>
                                    
                                    <td>
                                        <img src="{{ $amenity_details->picture ?: asset('placeholder.jpg') }}" alt="image"> 
                                    </td>

                                    <td>
                                        <a href="{{route('admin.amenities.view' , ['amenity_id' => $amenity_details->id] )}}"> {{$amenity_details->type}}
                                        </a>
                                    </td>

                                    <td>
                                        {{$amenity_details->value}}
                                    </td>

                                    <td>                                    
                                        @if($amenity_details->status == APPROVED)

                                            <span class="badge badge-outline-success">
                                                {{ tr('approved') }} 
                                            </span>

                                        @else

                                            <span class="badge badge-outline-danger">
                                                {{ tr('declined') }} 
                                            </span>
                                               
                                        @endif
                                    </td>
                                   
                                    <td>                                                                        
                                        <div class="dropdown">

                                            <button class="btn btn-outline-primary  dropdown-toggle btn-sm" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{tr('action')}}
                                            </button>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                <a class="dropdown-item" href="{{ route('admin.amenities.view', ['amenity_id' => $amenity_details->id] ) }}">{{tr('view')}}
                                                    </a>

                                                @if(Setting::get('is_demo_control_enabled') == NO)
                                                
                                                    <a class="dropdown-item" href="{{ route('admin.amenities.edit', ['amenity_id' => $amenity_details->id] ) }}">{{tr('edit')}}
                                                    </a>

                                                    <a class="dropdown-item" 
                                                    onclick="return confirm(&quot;{{tr('amenity_delete_confirmation' , $amenity_details->value)}}&quot;);" href="{{ route('admin.amenities.delete', ['amenity_id' => $amenity_details->id] ) }}" >
                                                        {{ tr('delete') }}
                                                    </a>
                                                    
                                                @else

                                                    <a class="dropdown-item" href="javascript:;">{{tr('edit')}}
                                                    </a>

                                                    <a class="dropdown-item" href="javascript:;">{{ tr('delete') }}
                                                    </a>

                                                @endif

                                                <div class="dropdown-divider"></div>

                                                @if($amenity_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{ route('admin.amenities.status', ['amenity_id' => $amenity_details->id] ) }}" 
                                                    onclick="return confirm(&quot;{{$amenity_details->value}} - {{tr('amenity_decline_confirmation')}}&quot;);"> 
                                                        {{tr('decline')}}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.amenities.status', ['amenity_id' => $amenity_details->id] ) }}">
                                                        {{tr('approve')}}
                                                    </a>
                                                       
                                                @endif
                                                
                                            </div>
                                             
                                        </div>
                                        
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