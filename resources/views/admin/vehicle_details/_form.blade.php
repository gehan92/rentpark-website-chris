<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                @if(Setting::get('is_demo_control_enabled') == NO)

                <form class="forms-sample" action="{{ route('admin.vehicle_details.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else

                <form class="forms-sample" role="form">

                @endif 

                    @csrf

                    <div class="card-header bg-card-header ">

                        <h4 class="">{{tr('vehicle_details')}}</h4>

                    </div>

                    <div class="card-body">

                        <input type="hidden" name="user_id" id="user_id" value="{{$vehicle_details->user_id}}">

                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{$vehicle_details->id}}">

                        <div class="row">

                            <div class="form-group col-md-4">
                                <label for="vehicle_number">{{ tr('vehicle_number') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="{{ tr('vehicle_number') }}" value="{{ old('vehicle_number') ?: $vehicle_details->vehicle_number}}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="vehicle_type">{{ tr('vehicle_type') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" placeholder="{{ tr('vehicle_type') }}" value="{{ old('vehicle_type') ?: $vehicle_details->vehicle_type}}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="vehicle_brand">{{ tr('vehicle_brand') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control" id="vehicle_brand" name="vehicle_brand" placeholder="{{ tr('vehicle_brand') }}" value="{{ old('vehicle_brand') ?: $vehicle_details->vehicle_brand}}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="vehicle_model">{{ tr('vehicle_model') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" placeholder="{{ tr('vehicle_model') }}" value="{{ old('vehicle_model') ?: $vehicle_details->vehicle_model}}" required>
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