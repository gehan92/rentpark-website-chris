<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">  

            <form class="forms-sample" action="{{ Setting::get('is_demo_control_enabled') == NO ? route('admin.provider_subscriptions.save') : '#'}}" method="POST" enctype="multipart/form-data" role="form">

                @csrf

                    <div class="card-header bg-card-header ">

                        <h4 class="">{{tr('provider_subscription')}}

                            <a class="btn btn-secondary pull-right" href="{{route('admin.provider_subscriptions.index')}}">
                                <i class="fa fa-eye"></i> {{tr('view_provider_subscriptions')}}
                            </a>
                        </h4>

                    </div>

                    <div class="card-body">

                        <input type="hidden" name="provider_subscription_id" id="provider_subscription_id" value="{{$provider_subscription_details->id}}">

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="title" class="">{{ tr('title') }} <span class="admin-required">*</span></label>

                                <input type="text" name="title" class="form-control" id="title" value="{{ old('title') ?: $provider_subscription_details->title }}" placeholder="{{ tr('title') }}" required >
                                
                            </div>
                            
                            <!-- <div class="form-group"> -->
                            <div class="form-group col-md-6">

                                <label for="plan">{{ tr('no_of_months') }} <span class="admin-required">*</span></label>

                                <input type="number" min="1" max="12" required name="plan" class="form-control" id="plan" value="{{ old('plan') ?: $provider_subscription_details->plan }}" title="{{ tr('plan') }}" placeholder="{{ tr('no_of_months') }}">
                            </div>

                        </div>

                        <div class="row">
                        
                            <div class="form-group col-md-6">

                                <label for="amount" class="">{{ tr('amount') }} <span class="admin-required">*</span></label>

                                <input type="number" value="{{ old('amount') ?: $provider_subscription_details->amount }}" name="amount" class="form-control" id="amount" placeholder="{{ tr('amount') }}" min="0" step="any" required>
                            </div>

                        </div>

                        <!--    <div class="row">

                            <div class="form-group col-md-6">

                                <label>{{tr('upload_image')}}</label>

                                <input type="file" name="picture" class="file-upload-default" accept="image/*">

                                <div class="input-group col-xs-12">

                                    <input type="text" class="form-control file-upload-info" disabled placeholder="{{tr('upload_image')}}">

                                    <div class="input-group-append">
                                        <button class="file-upload-browse btn btn-info" type="button">{{tr('upload')}}</button>
                                    </div>
                                </div>
                            </div>

                        </div> -->

                        <div class="row">

                            <div class="form-group col-md-12">

                                <label for="simpleMde">{{ tr('description') }}</label>

                                <textarea class="form-control" id="description" name="description">{{ old('description') ?: $provider_subscription_details->description}}</textarea>

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
    
</div>

