@extends('layouts.admin') 

@section('title', tr('view_questions'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">{{tr('questions')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_questions') }}</span>
    </li>
 
@endsection 

@section('content')

    <div class="col-lg-12 grid-margin stretch-card">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{tr('view_questions')}}

                    <a class="btn btn-secondary pull-right" href="{{route('admin.questions.create')}}">
                        <i class="fa fa-plus"></i> {{tr('add_question')}}
                    </a>

                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
 
                    <table id="order-listing" class="table">
 
                        <thead>
                            <tr>
                                <th>{{tr('s_no')}}</th>
                                <th>{{ tr('user_question') }}</th>
                                <th>{{tr('category')}}</th>
                                <th>{{tr('sub_category')}}</th>
                                <th>{{tr('type')}}</th>
                                <th>{{tr('question_type')}}</th>
                                <th>{{tr('status')}}</th>
                                <th>{{tr('action')}}</th>
                            </tr>
                        </thead>
 
                        <tbody>

                        @foreach($questions as $i => $question_details)

                            <tr>
                                
                                <td>{{$i+1}}</td>
                                
                                <td>
                                    <a href="{{route('admin.questions.view' , $question_details->id)}}">
                                        {{$question_details->user_question}}
                                    </a>
                                </td>
                                
                                <td>
                                    <a href="{{route('admin.categories.view' , ['category_id' => $question_details->category_id] )}}">  
                                        {{$question_details->categoryDetails ? $question_details->categoryDetails->name : ""}}
                                    </a>
                                    
                                </td>

                                <td>
                                    <a href="{{route('admin.sub_categories.view' , ['sub_category_id' => $question_details->sub_category_id] )}}">  
                                        {{$question_details->subCategoryDetails ? $question_details->subCategoryDetails->name : ""}}
                                    </a>

                                </td>

                                <td>
                                    {{$question_details->question_static_type}}
                                </td>

                                <td>
                                    {{$question_details->question_input_type}}
                                </td>
                                
                                <td>                                    
                                    @if($question_details->status == APPROVED)

                                        <a class="badge badge-success" href="{{ route('admin.questions.status', $question_details->id) }}">
                                            {{tr('approved')}}
                                        </a>

                                    @else

                                        <a class="badge badge-danger" href="{{ route('admin.questions.status', $question_details->id) }}"> 
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

                                            <a class="dropdown-item" href="{{ route('admin.questions.view', $question_details->id) }}">
                                                {{tr('view')}}
                                            </a>

                                            @if(Setting::get('is_demo_control_enabled') == NO)

                                                <a class="dropdown-item" href="{{ route('admin.questions.edit', $question_details->id) }}">
                                                    {{tr('edit')}}
                                                </a>

                                                <a class="dropdown-item" 
                                                onclick="return confirm(&quot;{{tr('question_delete_confirmation' , $question_details->user_question)}}&quot;);" href="{{ route('admin.questions.delete',['common_question_id'=> $question_details->id] ) }}" >
                                                    {{ tr('delete') }}
                                                </a>


                                            @else

                                                <a class="dropdown-item" href="javascript:;">{{tr('edit')}}</a>

                                                <a class="dropdown-item" href="javascript:;">{{ tr('delete') }}</a>

                                            @endif

                                            <div class="dropdown-divider"></div>

                                            @if($question_details->status == APPROVED)
                                                
                                                <a class="dropdown-item" href="{{ route('admin.questions.status', ['common_question_id' => $question_details->id] ) }}"> 
                                                    {{tr('decline')}}
                                                </a>
                                                   
                                            @else

                                                <a class="dropdown-item" href="{{ route('admin.questions.status', ['common_question_id' => $question_details->id] ) }}">
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