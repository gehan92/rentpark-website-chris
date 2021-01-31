
<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                @if(Setting::get('is_demo_control_enabled') == NO )

                    <form class="forms-sample" action="{{ route('admin.service_locations.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else

                    <form class="forms-sample" role="form">

                @endif 

                @csrf
                
                    <div class="card-header bg-card-header">

                        <h4 class="">{{tr('service_location')}}
                            <a class="btn btn-secondary pull-right" href="{{route('admin.service_locations.index')}}">
                                <i class="fa fa-eye"></i> {{tr('view_service_locations')}}
                            </a>
                            
                        </h4>

                    </div>

                    <div class="card-body">

                        <input type="hidden" name="service_location_id" id="service_location_id" value="{{$service_location_details->id}}">

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="name"> {{ tr('service_location_name') }} <span class="admin-required">*</span></label>

                                <input type="text" class="form-control" name="name" placeholder="{{ tr('name') }}" value="{{ old('name') ?: $service_location_details->name}}" required >

                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') ?: $service_location_details->latitude }}">

                                <input type="hidden" name="longitude" id="longitude" value="{{ old('latitude') ?: $service_location_details->latitude}}"> 
                           
                            </div>

                            <div class="form-group col-md-6">

                                <label for="address">{{ tr('service_location_center') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control"  id="my-dest" onFocus="geolocate()" name="address" placeholder="{{ tr('address') }}" value="{{ old('name') ?: $service_location_details->address}}" required>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="radius">{{ tr('radius') }} <span class="admin-required">*</span></label>

                                <input type="number" class="form-control" id="radius" name="cover_radius" placeholder="{{ tr('radius') }} in {{ tr('km') }}" value="{{ old('cover_radius') ?: $service_location_details->cover_radius}}" required>
                            </div>  

                            <div class="form-group col-md-6">

                                <label>{{tr('upload_image')}}</label>

                                <input type="file" name="picture" class="file-upload-default" accept="image/*">

                                <div class="input-group col-xs-12">

                                    <input type="file" class="form-control file-upload-info" name="picture" placeholder="{{tr('upload_image')}}" accept="image/*">

                                    <div class="input-group-append">
                                        <button class="btn btn-info" type="button">{{tr('upload')}}</button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-12">

                                <label for="description">{{ tr('description') }} <span class="admin-required">*</span></label>

                                <textarea class="form-control" id="description" name="description" >{{ old('description') ?: $service_location_details->description}}</textarea>

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

