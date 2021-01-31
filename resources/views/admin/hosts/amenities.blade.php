@extends('layouts.admin') 

@section('title', tr('add_host'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.hosts.index') }}">{{tr('hosts')}}</a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('admin.spaces.view',['host_id' => $host->id])}}">{{tr('view_host')}}</a>
    </li>
           
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('update_amenities')}}</span>
    </li>
@endsection 

@section('styles')

    <link rel="stylesheet" href="{{ asset('admin-assets/css/host.css')}} ">   

@endsection

@section('content')

<div class="row">
    
    <div class="col-12 grid-margin">

        <div class="card card-outline-info">

            <div class="card-body">
                <h5 class="card-title">{{tr('update_amenities')}}</h5>

                <hr>
                @if(count($amenties)> 0 || count($others) > 0)
                <form class="forms-sample" id="example-form" action="{{ route('admin.hosts.amenities.save') }}"  method="POST" enctype="multipart/form-data" role="form">

                @csrf

                    <div>

                        <h3 class="text-uppercase">{{tr('amenities')}}</h3>

                        <section>

                            <!-- <h4>Basic Host details</h4> -->

                            <div class="row">
                                
                                <div class="col-lg-6 grid-margin grid-margin-lg-0">
                                    
                                    <div class="card-body">
                                        
                                        <input type="hidden" name="host_id" id="host_id" value="{{ $host->id }}">

                                        @foreach($amenties as $amenties_key => $amenitie_details)

                                        @if($amenitie_details->answers)
                                            <h4 class="card-description">{{$amenitie_details->provider_question}}</h4>

                                            @if($amenitie_details->question_input_type == 'checkbox')

                                                @foreach($amenitie_details->answers as $key=>$answers)

                                                <div class="icheck-square">
                                                    <input tabindex="5" type="checkbox" name="common_question_answers[{{$amenitie_details->id}}][{{$answers->id}}]" @if($answers->is_selected == YES) checked @endif value="{{ $answers->value }}">

                                                    <span>{{$answers->value}}</span>
                                                </div>
                                                @endforeach
                                                <hr>

                                            @elseif($amenitie_details->question_input_type == 'input')

                                                <input type="text" class="form-control" name="user_answers[{{$amenitie_details->id}}]" placeholder="User Answer" value="{{ old('user_question') ?: $amenitie_details->answer }}"> 
                                                <hr>

                                            @elseif($amenitie_details->question_input_type == 'select')

                                                <select id="select_option" class="form-control text-uppercase" name="select_option[{{$amenitie_details->id}}]" required >

                                                    <option value="">{{ tr('choose')}}</option>

                                                    @foreach($amenitie_details->answers as $key=>$answers)
                                                        <option value="{{$answers->id}}" @if($answers->is_selected == YES) selected @endif> {{$answers->value}} </option>

                                                    @endforeach
                                                    
                                               </select>
                                            <hr>
                                            @elseif($amenitie_details->question_input_type == 'radio')
                                                
                                                @foreach($amenitie_details->answers as $key=>$answers)
                                                <div class="form-radio form-radio-flat">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" name="radio_option[{{$amenitie_details->id}}]" id="flatRadios2" @if($answers->is_selected == YES) checked @endif value="{{ $answers->id }}"> {{$answers->value}}
                                                    </label>
                                                </div>
                                                @endforeach
                                                <hr>
                                            @endif
                                        @endif
                                        @endforeach
                                      
                                    </div>
                                </div>
                            </div>
                            
                        </section>


                        <h3>{{tr('others')}}</h3>

                        <section>

                            <!-- <h4>Basic Host details</h4> -->

                            <div class="row">
                                <div class="col-lg-6 grid-margin grid-margin-lg-0">
                                    <div class="card-body">
                                        <input type="hidden" name="host_id" id="host_id" value="{{$host->id}}">

                                        @foreach($others as $amenties_key => $other_details)


                                        <h4 class="card-description">{{$other_details->provider_question}}</h4>


                                        @if($other_details->question_input_type == 'checkbox')

                                            @foreach($other_details->answers as $key=>$answers)

                                            <div class="icheck-square">
                                                <input tabindex="5" type="checkbox" name="common_question_answers[{{$other_details->id}}][{{$answers->id}}]" @if($answers->is_selected == YES) checked @endif value="{{ $answers->value }}">

                                                <span>{{$answers->value}}</span>
                                            </div>
                                            @endforeach
                                            <hr>

                                        @elseif($other_details->question_input_type == 'input')
                                            <input type="text" class="form-control" name="user_answers[{{$other_details->id}}]" placeholder="User Answer" value="{{ old('user_question') ?: $other_details->answer }}">
                                            <hr>

                                        @elseif($other_details->question_input_type == 'select')

                                            <select id="select_option" class="form-control text-uppercase select2" name="select_option[{{$other_details->id}}]" required >

                                                <option value="">{{ tr('choose')}}</option>

                                                @foreach($other_details->answers as $key=>$answers)
                                                    <option value="{{$answers->id}}" @if($answers->is_selected == YES) selected @endif> {{$answers->value}} </option>

                                                @endforeach
                                                
                                                
                                           </select>
                                       
                                        @elseif($other_details->question_input_type == 'radio')
                                            
                                            @foreach($other_details->answers as $key=>$answers)
                                            
                                            <div class="form-radio form-radio-flat">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="radio_option[{{$other_details->id}}]" id="flatRadios2" @if($answers->is_selected == YES) checked @endif value="{{ $answers->id }}"> {{$answers->value}}

                                                </label>
                                            </div>
                                            @endforeach
                                            <hr>
                                        @endif

                                        @endforeach
                                      
                                    </div>
                                </div>
                            </div>
                            
                        </section>
                        
                    </div>
                </form>
                @else  
                <h5>
                {{tr('questions_create_message')}} <a href="{{ route('admin.questions.create') }}">{{tr('add_question')}}
                </a></h5>
                @endif
            </div>
        </div>
    </div>

</div>


   
@endsection

@section('scripts')

<script src="{{ asset('admin-assets/node_modules/jquery-steps/build/jquery.steps.min.js')}}"></script>
 
<script src="{{ asset('admin-assets/node_modules/jquery-validation/dist/jquery.validate.min.js')}}"></script>

<script src="{{ asset('admin-assets/js/wizard.js')}}"></script>


@endsection