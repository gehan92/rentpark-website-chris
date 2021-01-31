@extends('layouts.admin') 

@section('title', tr('documents'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.documents.index') }}">{{tr('documents')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_documents') }}</span>
    </li>
           
@endsection 

@section('content')

    <div class="col-lg-12 grid-margin stretch-card">
        
        <div class="card">

            <div class="card-header bg-card-header">

                <h4 class="">{{tr('documents')}}

                    <a class="btn btn-secondary pull-right" href="{{route('admin.documents.create')}}">
                        <i class="fa fa-plus"></i> {{tr('add_document')}}
                    </a>
                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table id="order-listing" class="table">

                        <thead>
                            <tr>
                                <th>{{tr('s_no')}}</th>
                                <th>{{tr('name')}}</th>
                                <th>{{tr('status')}}</th>
                                <th>{{tr('action')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                         
                        @foreach($documents as $i => $document_details)

                            <tr>
                                <td>{{$i+1}}</td>
                               
                                <td>
                                    <a href="{{route('admin.documents.view' , ['document_id' => $document_details->id] )}}"> {{$document_details->name}}
                                    </a>
                                </td>
                                
                                <td>                                    
                                    @if($document_details->status == APPROVED)

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
                                  
                                    <div class="template-demo">
                                  
                                        <div class="dropdown">
                                           
                                            <button class="btn btn-outline-primary  dropdown-toggle btm-sm" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{tr('action')}}
                                            </button>
                                            
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">
                                            
                                                <a class="dropdown-item" href="{{ route('admin.documents.view',['document_id' => $document_details->id]) }}">
                                                      {{tr('view')}}
                                                  </a>

                                                @if(Setting::get('is_demo_control_enabled') == NO)
                                                
                                                    <a class="dropdown-item" href="{{ route('admin.documents.edit',['document_id' => $document_details->id]) }}">
                                                        {{tr('edit')}}
                                                    </a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{tr('document_delete_confirmation' , $document_details->name)}}&quot;);" href="{{ route('admin.documents.delete',['document_id' => $document_details->id]) }}">
                                                        {{ tr('delete') }}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="javascript:;">
                                                        {{tr('edit')}}
                                                    </a>

                                                    <a class="dropdown-item" href="javascript:;">
                                                        {{ tr('delete') }}
                                                    </a>

                                                @endif  

                                                <div class="dropdown-divider"></div>

                                                @if($document_details->status == APPROVED)

                                                    <a class="dropdown-item" href="{{ route('admin.documents.status',['document_id' => $document_details->id]) }}" onclick="return confirm(&quot;{{ $document_details->name }}-{{tr('document_decline_confirmation' , $document_details->name)}}&quot;);">

                                                        {{tr('decline')}}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.documents.status',['document_id' => $document_details->id]) }}">
                                                        {{tr('approve')}}
                                                    </a>
                                                       
                                                @endif
                                             

                                            </div>
                           
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