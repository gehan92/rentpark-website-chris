@extends('layouts.admin') 

@section('title', tr('view_categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="javascript:;" >{{ tr('categories')}}</a></li>
    
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">{{ tr('view_categories') }}</a></li>  

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('questions') }}</span>
    </li>
           
@endsection 

@section('content')
  
    @if($module == CATEGORIES)
        <div class="row col-lg-12 grid-margin">
            <div class="col-lg-12"><h4>{{ tr('category') }} : <a href="javascript:;">{{  $module_details->name}} </a></h4></div>
        </div>
    @elseif($module == SUB_CATEGORIES)
        <div class="row col-lg-12 grid-margin">
            <div class="col-lg-12"><h4>{{ tr('sub_category') }} : <a href="javascript:;">{{  $module_details->name}} </a></h4></div>
        </div>
    @endif
  
    <div class="col-lg-12 grid-margin stretch-card">
        
        <div class="card">

            <div class="card-header bg-card-header ">

                <h4 class="">{{ tr('view_questions') }}

                    <a class="btn btn-secondary pull-right" href="{{ route('admin.questions.create')}}">
                        <i class="fa fa-plus"></i> {{ tr('add_question') }}
                    </a>
                </h4>

            </div>

            <div class="card-body">

                <div class="table-responsive">
                    
                    <table id="order-listing" class="table">
                      
                        <thead>
                            <tr>
                                <th>{{ tr('s_no') }}</th>
                                <th>{{ tr('category') }}</th>
                                <th>{{ tr('sub_category') }}</th>
                                <th>{{ tr('provider_name') }}</th>
                                <th>{{ tr('question')  }}</th>
                                <th>{{ tr('action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                         
                            @foreach($questions as $i => $question_details)

                                <tr>
                                    <td>{{ $i+1}}</td>
                                    
                                    <td> <a href="{{route('admin.categories.view' , ['category_id' => $question_details->category_id] )}}"> {{ $question_details->category_name }} </a></td>

                                    <td><a href="{{route('admin.sub_categories.view' , ['sub_category_id' => $question_details->sub_category_id] )}}"> {{ $question_details->sub_category_name }} </a> </td>

                                    <td>
                                        {{ $question_details->provider_name }}
                                    </td>  

                                    <td>

                                        @if($module == CATEGORIES)
                                         
                                            <a href="{{ route('admin.questions.index', ['category_id' => $question_details->category_id])}}"> {{  $question_details->common_questions_count }}</a>
                                        
                                        @elseif($module == SUB_CATEGORIES)
                                         
                                            <a href="{{ route('admin.questions.index', ['sub_category_id' => $question_details->sub_category_id])}}"> {{  $question_details->common_questions_count }}</a> 
                                       
                                        @endif
                                        
                                    </td>
                                   
                                    <td><a href="{{ route('admin.questions.index', ['sub_category_id' => $question_details->sub_category_id])}}"><span class="btn btn-info btn-large">{{ tr('view') }}</span></a>
                                         
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