@if(in_array($question_input_type , ['input' , 'textarea' , 'file']))

@elseif(in_array($question_input_type , ['select' , 'checkbox' , 'radio']))
	<button data-repeater-create type="button" class="btn btn-info btn-sm icon-btn ml-2 mb-2"><i class="mdi mdi-plus"></i> </button>

	<div data-repeater-list="group-a">

        <div data-repeater-item>

        	<div class="row">

        		<div class="col-md-12">

        			<h4 class="answer-section-header">Answer Section <?php // $currentIndex; ?></h4>

        		</div>
		        
		        @if(count($question_answer_details) > 0)
		        	@foreach($question_answer_details as $key => $value)
		        		<input type="hidden" name="common_question_answer_id" id="common_question_answer_id" value="{{$value->id}}">
        				<div class="col-md-12">

				    		<div class="form-group">

					        	<div class="input-group">
					                
					                <input type="text" class="form-control" name="answers" placeholder="User Answer" value="{{$value->common_answer}}"> 

					                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
					    		
					            </div>
				            </div>

				        </div>
				        <div class="col-md-12">

			            <div class="form-group">

				        	<div class="input-group">
				                
				                <input type="text" class="form-control" name="provider_answers" placeholder="Provider Answer" value="{{$value->common_provider_answer}}">

				                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
				    		
				            </div>
			            </div>
		           
		           </div>
		        	@endforeach
		        @else
		        	<div class="col-md-12">

			    		<div class="form-group">

				        	<div class="input-group">
				                
				                <input type="text" class="form-control" name="answers" placeholder="User Answer">

				                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
				    		
				            </div>
			            </div>

			        </div>

			       <div class="col-md-12">

			            <div class="form-group">

				        	<div class="input-group">
				                
				                <input type="text" class="form-control" name="provider_answers" placeholder="Provider Answer">

				                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
				    		
				            </div>
			            </div>
		           
		           </div>
		        @endif
	        </div>

    	</div>

    </div>
@elseif($question_input_type == "plus_minus")
	<div class="col-md-6">

		<div class="form-group">

        	<div class="input-group">
                
                <input type="text" class="form-control" name="answers[min_value]" id="Min value" placeholder="Min value">

                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
    		
            </div>
        </div>

	</div>

	<div class="col-md-6">

		<div class="form-group">

        	<div class="input-group">
                
                <input type="text" class="form-control" name="answers[default_value]" id="Min value" placeholder="Min value">

                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
    		
            </div>
        </div>

	</div>

	<div class="col-md-6">

		<div class="form-group">

        	<div class="input-group">
                
                <input type="text" class="form-control" name="answers[max_value]" id="Max Value" placeholder="Max Value">

                <button data-repeater-delete type="button" class="input-group-addon input-group-text btn text-danger"><i class="fa fa-trash"></i></button>
    		
            </div>
        </div>

	</div>
@endif
