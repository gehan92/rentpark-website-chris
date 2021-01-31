<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                @if(Setting::get('is_demo_control_enabled') == NO )

                <form class="forms-sample" action="{{ route('admin.amenities.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else

                <form class="forms-sample" role="form">

                @endif 

                    @csrf

                    <div class="card-header bg-card-header ">

                        <h4 class="">{{tr('amenity')}}

                            <a class="btn btn-secondary pull-right" href="{{route('admin.amenities.index')}}">
                                <i class="fa fa-eye"></i> {{tr('view_amenities')}}
                            </a>
                        </h4>

                    </div>

                    <div class="card-body">

                        <input type="hidden" name="amenity_id" id="amenity_id" value="{{ $amenity_details->id }}">

                        <div class="row">

                            <div class=" col-md-6">
                               
                                <div class="form-group">

                                    <label for="type">{{tr('choose_space_type')}} <span class="admin-required">*</span></label>

                                    <select class="form-control select2" id="type" name="type">

                                        <option value="">{{tr('choose_space_type')}}</option>

                                        @foreach($host_types as $host_type_details)
                                            <option value="{{$host_type_details->key}}" @if($host_type_details->is_selected == YES) selected @endif>{{$host_type_details->value}}</option>
                                        @endforeach
                                        
                                    </select>

                                </div>
                            
                            </div>
                                                        
                            <div class="form-group col-md-6">

                                <label for="value">{{ tr('name') }} <span class="admin-required">*</span></label>
                               
                                <input type="text" class="form-control" id="value" name="value" placeholder="{{ tr('value') }}" value="{{ old('value') ?: $amenity_details->value }}" required>

                            </div>

                        </div>

                        <div class="row">


                            <div class="form-group col-md-6">

                                <label>{{tr('upload_image')}} </label>

                                <input type="file" name="picture" class="file-upload-default" accept="image/*" >

                                <div class="input-group col-xs-12">

                                    <input type="file" class="form-control file-upload-info" name="picture" placeholder="{{tr('upload_image')}}" accept="image/*" >

                                    <div class="input-group-append">
                                        <button class="btn btn-info" type="button">{{tr('upload')}}</button>
                                    </div>
                                </div>
                            </div>

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