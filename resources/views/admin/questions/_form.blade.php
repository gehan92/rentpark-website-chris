
<div class="col-lg-12 grid-margin stretch-card">


    <div class="clearfix"></div>

    <br>
    
    <div class="row flex-grow">

        <div class="col-12 grid-margin">
            
            <div class="card">

                <div class="card-header bg-card-header ">

                    <h4 class="">{{tr('question')}}</h4>

                </div>

                @if(Setting::get('is_demo_control_enabled') == NO)

                <form class="forms-sample repeater" action="{{ route('admin.questions.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else
               
                <form class="forms-sample repeater" role="form">

                @endif

                    @csrf

                    @if($question_details->id)

                    <input type="hidden" name="common_question_id" id="common_question_id" value="{{$question_details->id}}">

                    @endif

                    <div class="card-body">

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="user_question">{{ tr('user_question') }} <span class="admin-required">*</span> </label>

                                <input type="text" class="form-control" id="user_question" name="user_question" placeholder="{{ tr('user_question') }}" value="{{ old('user_question') ?: $question_details->user_question }}" required> 

                            </div>

                            <div class="form-group col-md-6">

                                <label for="provider_question">{{ tr('provider_question') }} <span class="admin-required">*</span> </label>

                                <input type="text" class="form-control" id="provider_question" name="provider_question" placeholder="{{ tr('provider_question') }}" value="{{ old('provider_question') ?: $question_details->provider_question}}" required> 

                            </div>

                            <div class="form-group col-md-4" style="display: none;">

                                <label for="question_group">{{ tr('question_group') }}</label>

                                <select id="question_group_id" class="form-control select2" title="{{tr('choose_question_group')}}" name="question_group_id" >

                                    <option value="">{{ tr('choose_question_group') }}</option>

                                    @foreach($question_groups as $i => $question_group_details)

                                        <option value="{{ $question_group_details->id }}" @if($question_group_details->is_selected == YES) selected @endif> 

                                            {{ $question_group_details->group_name }}
                                       
                                        </option>

                                    @endforeach
                                    
                               </select>
                                
                            </div>

                            <div class="form-group col-md-4">

                                <label for="category">{{ tr('category') }}</label>

                                <select id="category_id" class="form-control select2" title="{{tr('select_category')}}" name="category_id" >

                                    <option value="">{{ tr('select_category') }}</option>

                                    @foreach($categories as $i => $category_details)

                                        <option value="{{ $category_details->id }}" @if($category_details->is_selected == YES) selected @endif> 

                                            {{ $category_details->name }}
                                       
                                        </option>

                                    @endforeach
                                    
                               </select>
                                
                            </div>

                            <div class="form-group col-md-4">
                                
                                <label>{{tr('choose_sub_category')}}</label>

                                <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                    <!-- Based on the category the sub categoris will load -->
                                    <option value="">{{tr('choose_sub_category')}}</option>

                                    @foreach($sub_categories as $i => $sub_category_details)

                                        <option value="{{ $sub_category_details->id }}" @if($sub_category_details->is_selected == YES) selected @endif> 

                                            {{ $sub_category_details->name }}
                                       
                                        </option>

                                    @endforeach


                                </select>

                            </div>
                                
                        </div>

                        <div class="row">

                            <div class="col-md-6" style="display: none;">

                                <div class="form-group">

                                    <label>{{tr('is_inventory')}}</label>
                                    <div class="form-radio form-radio-flat">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="is_inventory" id="flatRadios1" value="{{YES}}"> {{tr('YES')}}
                                        </label>
                                    </div>
                                    <div class="form-radio form-radio-flat">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="is_inventory" id="flatRadios2" value="{{NO}}" checked> {{tr('NO')}}
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group col-md-4">

                                <label for="question_static_type">{{ tr('select_type') }} <span class="admin-required">*</span> </label>

                                <select id="question_static_type" class="form-control select2" title="{{tr('select_type')}}" name="question_static_type" required >

                                    <option value="">{{ tr('select_type') }}</option>

                                    @foreach($question_static_types as $qst_key => $question_static_type)

                                    <option value="{{$qst_key}}" @if($qst_key == $question_details->question_static_type) selected @endif>{{ $question_static_type }}</option>

                                    @endforeach
                                    
                               </select>
                                
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">

                                    <label>{{tr('is_searchable')}}</label>
                                    <div class="form-radio form-radio-flat">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="is_searchable" id="flatRadios1" value="{{YES}}" @if($question_details->is_searchable == YES) checked @endif> {{tr('YES')}}
                                        </label>
                                    </div>
                                    <div class="form-radio form-radio-flat">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="is_searchable" id="flatRadios2" value="{{NO}}" @if($question_details->is_searchable == NO) checked @endif> {{tr('NO')}}
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="question_input_type" class="text-uppercase">{{ tr('question_input_type') }} <span class="admin-required">*</span> </label>

                                <select id="question_input_type" class="form-control text-uppercase select2" name="question_input_type" required >

                                    <option value="">{{ tr('choose_question_input_type') }}</option>

                                    <?php 
                                    // 'plus_minus' => "plus_minus", 'file' => 'file', @TODO
                                    $list_of_question_input_types = ['input' => "input", 'checkbox' => "checkbox", 'select' => "select" ,
                                        'radio' => 'radio']; ?>

                                    @foreach($list_of_question_input_types as $t => $list_of_question_input_type)

                                        <option value="{{$list_of_question_input_type}}" @if($list_of_question_input_type == $question_details->question_input_type) selected @endif > {{$list_of_question_input_type}} </option>

                                    @endforeach
                                    
                               </select>
                                
                            </div>

                        </div>

                        <div id="questions_answers">

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
        </div>

    </div>

</div>