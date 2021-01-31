@extends('layouts.admin') 

@section('title', tr('edit_question'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.questions.index') }}">{{tr('questions')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('edit_question')}}</span>
    </li>
           
@endsection

@section('scripts')

  	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>

  	<script>
  		var url = "{{route('admin.questions.getformanswers')}}";

  		var checkStatus = ['input' , 'textarea' , 'file'];

  		function checkType(question_input_type,common_question_id) {
	    	if($.inArray(question_input_type, checkStatus) !== -1) {
				$('#questions_answers').html("");

			} else {
				
		    	var data = {'question_input_type' : question_input_type,
		    		'common_question_id' : common_question_id,
		    	 	_token: '{{csrf_token()}}'};

		    	var request = $.ajax({
								url: url,
								type: "POST",
								data: data,
							});

				request.done(function(result) {

				  	$('#questions_answers').html(result);

				  	$('.repeater').repeater();

					$('#questions_answers').show();

				});

				request.fail(function(jqXHR, textStatus) {
				  	alert( "Request failed: " + textStatus );
				});
			}
	    }

  		var question_input_type = document.getElementById("question_input_type").value;

  		var common_question_id = document.getElementById("common_question_id").value;
  		
  		checkType(question_input_type,common_question_id);

		$(document).ready(function() {

		    'use strict';

		    $('.repeater').repeater();

		    $('#question_input_type').on('select2:select', function (e) {

		    	var question_input_type = $(this).val();

		    	var common_question_id = document.getElementById("common_question_id").value;

		    	checkType(question_input_type,common_question_id);
			});

		    
			$('#category_id').on('select2:select' , function (e) {

		    	var category_id = $(this).val();

		    	var sub_category_url = "{{route('admin.get_sub_categories')}}";

				var data = {'category_id' : category_id, _token: '{{csrf_token()}}'};

		    	var request = $.ajax({
								url: sub_category_url,
								type: "POST",
								data: data,
							});

				request.done(function(result) {

					if(result.success == true) {
						
						$("#sub_category_id").html(result.view);

						$("#sub_category_id").select2();
					}

				});

				request.fail(function(jqXHR, textStatus) {
				  	alert( "Request failed: " + textStatus );
				});

			});
		});
  	
  	</script>

@endsection 

@section('content') 

	@include('admin.questions._form') 

@endsection