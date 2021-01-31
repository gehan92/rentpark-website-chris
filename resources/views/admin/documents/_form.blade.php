<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                <div class="card-header bg-card-header ">

                    <h4 class="">{{tr('add_document')}}

                        <a class="btn btn-secondary pull-right" href="{{route('admin.documents.index')}}">
                            <i class="fa fa-eye"></i> {{tr('documents')}}
                        </a>
                    </h4>

                </div>

                @if(Setting::get('is_demo_control_enabled') == NO)

                <form class="forms-sample" action="{{ route('admin.documents.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else
                
                <form class="forms-sample" role="form">
                
                @endif

                @csrf
                    
                    <div class="card-body">

                        <input type="hidden" name="document_id" id="document_id" value="{{$document_details->id}}">

                        <div class="row">

                            <div class="form-group col-md-12">
                                <label for="name">{{ tr('name') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ tr('name') }}" value="{{ old('name') ?: $document_details->name}}" required>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-12">

                                <label for="simpleMde">{{ tr('description') }}</label>

                                <textarea class="form-control" id="description" name="description">{{ old('description') ?: $document_details->description}}</textarea>

                            </div>
                            
                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="reset" class="btn btn-light">{{ tr('reset')}}</button>

                        @if(Setting::get('is_demo_control_enabled') == NO)
                        
                            <button type="submit" class="btn btn-success mr-2">{{ tr('submit') }}</button>
                            
                        @else
                        
                            <button type="button" class="btn btn-success mr-2" disabled> {{ tr('submit') }}</button>
                            
                        @endif
                      
                    </div>

                </form>
            
            </div>

        </div>
    
    </div>

</div>