@extends('layouts.admin') 

@section('title', tr('view_question'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.questions.index')}}">{{tr('questions')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_question')}}</span>
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
                        <h4 class="card-title">{{ tr('user_question') }}</h4>
                        <!-- Text -->
                        <p class="card-text">{{ $question_details->user_question }}</p>

                        <!-- Title -->
                        <h4 class="card-title">{{ tr('provider_question') }}</h4>
                        <!-- Text -->
                        <p class="card-text">{{ $question_details->provider_question }}</p>
                        
                    </div>
                    <!-- Card content -->

                </div>
                <!-- Card -->

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('category')}}</h5>
                            
                            <p class="card-text">
                                <a href="{{route('admin.categories.view' ,['category_id' => $question_details->category_id])}}">
                                    {{$question_details->category_name}}
                                </a>
                            </p>

                        </div>

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('sub_category')}}</h5>
                            
                            <p class="card-text">
                                <a href="{{route('admin.sub_categories.view' ,['sub_category_id' => $question_details->sub_category_id] ) }}">
                                    {{$question_details->sub_category_name}}
                                </a>
                            </p>

                        </div>  

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('group_name')}}</h5>
                            
                            <p class="card-text">
                                {{$question_details->commonQuestionGroupDetails->group_name ?? '-'}}
                            </p>

                        </div> 

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('status')}}</h5>
                            
                            <p class="card-text">

                                @if($question_details->status == APPROVED)

                                    <span class="badge badge-success badge-md text-uppercase">
                                        {{tr('approved')}}
                                    </span>

                                @else 

                                    <span class="badge badge-danger badge-md text-uppercase">
                                        {{tr('pending')}}
                                    </span>

                                @endif
                            
                            </p>

                        </div>
                                                
                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('updated_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($question_details->updated_at) }}</p>

                        </div>

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('created_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($question_details->created_at) }}</p>

                        </div> 

                    </div>
                    <!-- Card content -->

                </div>

                <!-- Card -->

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        @if(Setting::get('is_demo_control_enabled') == NO )

                            <a href="{{ route('admin.questions.edit', $question_details->id) }}" class="btn btn-primary btn-block">
                                {{tr('edit')}}
                            </a>

                            <a onclick="return confirm(&quot;{{tr('question_delete_confirmation' , $question_details->user_question)}}&quot;);" href="{{ route('admin.questions.delete', ['common_question_id'=> $question_details->id] ) }}"  class="btn btn-danger btn-block">
                                {{tr('delete')}}
                            </a>

                        @else
                            <a href="javascript:;" class="btn btn-primary btn-block">{{tr('edit')}}</a>

                            <a href="javascript:;" class="btn btn-danger btn-block">{{tr('delete')}}</a>

                        @endif

                        @if($question_details->status == APPROVED)

                            <a class="btn btn-danger btn-block" href="{{ route('admin.questions.status', ['common_question_id'=> $question_details->id] ) }}" 
                            onclick="return confirm(&quot;{{$question_details->user_question}} - {{tr('question_decline_confirmation')}}&quot;);"> 
                                {{tr('decline')}}
                            </a>

                        @else

                            <a class="btn btn-success btn-block" href="{{ route('admin.questions.status', ['common_question_id'=> $question_details->id] ) }}">
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