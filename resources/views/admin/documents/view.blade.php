@extends('layouts.admin') 

@section('title', tr('view_document'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.documents.index')}}">{{tr('documents')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_document')}}</span>
    </li>
           
@endsection  

@section('content')

    <div class="row">

        <div class="col-md-12">

            <!-- Card group -->
            <div class="card-group">

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        <!-- Title -->
                        <h4 class="card-title">{{ tr('description') }}</h4>
                        <!-- Text -->
                        <p class="card-text">{{ $document_details->description }}</p>
                        
                    </div>
                    <!-- Card content -->

                </div>
                <!-- Card -->

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('name')}}</h5>
                            
                            <p class="card-text">{{$document_details->name}}</p>

                        </div> 

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('status')}}</h5>
                            
                            <p class="card-text">

                                @if($document_details->status == APPROVED)

                                    <span class="badge badge-success badge-md text-uppercase">{{tr('approved')}}</span>

                                @else 

                                    <span class="badge badge-danger badge-md text-uppercase">{{tr('pending')}}</span>

                                @endif
                            
                            </p>

                        </div>
                                                
                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('updated_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($document_details->updated_at) }}</p>

                        </div>

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('created_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($document_details->created_at) }}</p>

                        </div> 

                    </div>
                    <!-- Card content -->

                </div>

                <!-- Card -->

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        @if(Setting::get('is_demo_control_enabled') == NO)
                        
                            <a href="{{ route('admin.documents.edit',['document_id' => $document_details->id] ) }}" class="btn btn-primary btn-block">
                                {{tr('edit')}}
                            </a>

                            <a onclick="return confirm(&quot;{{tr('document_delete_confirmation' , $document_details->name)}}&quot;);" href="{{ route('admin.documents.delete',['document_id' => $document_details->id] ) }}" class="btn btn-danger btn-block">
                                {{ tr('delete') }}
                            </a>

                        @else
                        
                            <button class="btn btn-primary btn-block" disabled>{{ tr('edit') }}</button>

                            <button class="btn btn-warning btn-block" disabled>{{ tr('delete') }}</button>

                        @endif

                        @if($document_details->status == APPROVED)

                            <a class="btn btn-warning btn-block" href="{{ route('admin.documents.status',['document_id' => $document_details->id] ) }}" onclick="return confirm(&quot;{{ $document_details->name }}-{{tr('document_decline_confirmation' , $document_details->name)}}&quot;);">

                                {{tr('decline')}}
                            </a>

                        @else

                            <a class="btn btn-success btn-block" href="{{ route('admin.documents.status',['document_id' => $document_details->id] ) }}">
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