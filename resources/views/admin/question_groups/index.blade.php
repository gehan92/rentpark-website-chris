@extends('layouts.admin') 

@section('title', tr('view_question_groups'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="javascript:;">{{tr('questions')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{ tr('view_question_groups') }}</span>
    </li>
           
@endsection 

@section('content')

    <div class="col-lg-12 grid-margin stretch-card">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">

                    {{tr('view_question_groups')}}

                    <!-- <a class="btn btn-secondary pull-right" href="{{route('admin.providers.create')}}"> -->
                        <!-- <i class="fa fa-plus"></i> {{tr('add_question_group')}} -->
                    <!-- </a> -->

                    <button type="button" class="btn btn-secondary pull-right" data-toggle="modal" data-target="#addQuestionGroup"><i class="fa fa-plus"></i> {{tr('add_question_group')}}</button>

                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
                    
                    <table id="order-listing" class="table">

                        <thead>
                            <tr>
                                <th>{{tr('s_no')}}</th>
                                <th>{{tr('group_name')}}</th>
                                <th>{{tr('provider_group_name')}}</th>
                                <th>{{tr('status')}}</th>
                                <th>{{tr('action')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                         
                            @foreach($question_groups as $i => $question_group_details)

                                <tr>
                                    <td>{{$i+1}}</td>
                                    
                                    <td>
                                        {{$question_group_details->group_name}}
                                    </td>

                                    <td>
                                        {{$question_group_details->provider_group_name}}
                                    </td>

                                    <td>                                    
                                        @if($question_group_details->status == APPROVED)

                                            <a class="badge badge-success" href="{{ route('admin.question_groups.status', $question_group_details->id) }}">
                                                {{tr('approved')}}
                                            </a>

                                        @else

                                            <a class="badge badge-danger" href="{{ route('admin.question_groups.status', $question_group_details->id) }}"> 
                                                {{tr('declined')}}
                                            </a>
                                               
                                        @endif
                                    </td>

                                    <td>

                                        <div class="dropdown">

                                            <button class="btn btn-outline-primary  dropdown-toggle" type="button" id="dropdownMenuOutlineButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{tr('action')}}
                                            </button>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                <!-- <a class="dropdown-item" href="{{ route('admin.question_groups.view', $question_group_details->id) }}">

                                                    {{tr('view')}}
                                                </a> -->

                                                @if(Setting::get('is_demo_control_enabled') == NO)
                                                
                                                    <a class="dropdown-item" data-toggle="modal" data-target="#editQuestionGroup{{$question_group_details->id}}">
                                                        {{tr('edit')}}
                                                    </a>

                                                    <a class="dropdown-item" 
                                                    onclick="return confirm(&quot;{{tr('question_group_delete_confirmation' , $question_group_details->group_name)}}&quot;);" href="{{ route('admin.question_groups.delete', ['question_group_id' => $question_group_details->id]) }}" >
                                                        {{ tr('delete') }}
                                                    </a>

                                                @else

                                                    <a class="dropdown-item" href="javascript:;">{{tr('edit')}}</a>

                                                    <a class="dropdown-item" href="javascript:;">{{ tr('delete') }}</a>
                                                    
                                                @endif

                                                <div class="dropdown-divider"></div>

                                                @if($question_group_details->status == APPROVED)

                                                    <a href="{{ route('admin.question_groups.status', ['question_group_id' => $question_group_details->id]) }}" class="dropdown-item text-danger">
                                                        {{tr('decline')}}
                                                    </a>

                                                @else 

                                                    <a href="{{ route('admin.question_groups.status', ['question_group_id' => $question_group_details->id]) }}" class="dropdown-item">
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


    <div class="modal fade" id="addQuestionGroup" tabindex="-1" role="dialog" aria-labelledby="addQuestionGroup" aria-hidden="true">
        
        <div class="modal-dialog" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase" id="addQuestionGroup-title">{{tr('add_question_group')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form class="" id="add_question_group_form" action="{{route('admin.question_groups.save')}}" method="POST">

                    @csrf

                    <div class="modal-body">
                    
                        <div class="form-group">
                            <label for="group_name" class="col-form-label">{{tr('group_name')}}:</label>
                            <input type="text" class="form-control" name="group_name" id="group_name" value="{{old('group_name')}}">
                        </div>

                        <div class="form-group">
                            <label for="provider_group_name" class="col-form-label">{{tr('provider_group_name')}}:</label>
                            <input type="text" class="form-control" name="provider_group_name" id="provider_group_name" value="{{old('provider_group_name')}}">
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{tr('submit')}}</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{tr('close')}}</button>
                    </div>

                </form>
            
            </div>
        </div>

    </div>

    @foreach($question_groups as $i => $question_group_details)

        <div class="modal fade" id="editQuestionGroup{{$question_group_details->id}}" tabindex="-1" role="dialog" aria-labelledby="editQuestionGroup{{$question_group_details->id}}" aria-hidden="true">
            
            <div class="modal-dialog" role="document">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase" id="editQuestionGroup{{$question_group_details->id}}-title">{{tr('edit_question_group')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{route('admin.question_groups.save')}}" method="POST">

                        @csrf

                        <div class="modal-body">

                            <input type="hidden" name="common_question_group_id" value="{{$question_group_details->id}}">
                        
                            <div class="form-group">
                                <label for="group_name" class="col-form-label">{{tr('group_name')}}:</label>
                                <input type="text" class="form-control" name="group_name" id="group_name" value="{{old('group_name') ?: $question_group_details->group_name}}">
                            </div>

                            <div class="form-group">
                                <label for="provider_group_name" class="col-form-label">{{tr('provider_group_name')}}:</label>
                                <input type="text" class="form-control" name="provider_group_name" id="provider_group_name" value="{{old('provider_group_name') ?: $question_group_details->provider_group_name}}">
                            </div>
                        
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{tr('submit')}}</button>
                            <button type="button" class="btn btn-light" data-dismiss="modal">{{tr('close')}}</button>
                        </div>

                    </form>
                
                </div>
           
            </div>

        </div>

    @endforeach

@endsection