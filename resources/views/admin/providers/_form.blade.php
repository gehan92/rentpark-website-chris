
<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                @if(Setting::get('is_demo_control_enabled') == NO)

                <form class="forms-sample" action="{{ route('admin.providers.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else       
               
                <form class="forms-sample" role="form">
                
                @endif

                    @csrf

                    <div class="card-header bg-card-header">

                        <h4 class="">{{tr('provider')}}
                            <a class="btn btn-secondary pull-right" href="{{route('admin.providers.index')}}">
                                <i class="fa fa-eye"></i> {{tr('view_providers')}}
                            </a>
                            
                        </h4>

                    </div>

                    <div class="card-body">

                        <input type="hidden" name="provider_id" id="provider_id" value="{{ $provider_details->id }}">

                        <input type="hidden" name="billing_info_id" id="billing_info_id" value="{{$provider_billing_info->id ?? ''}}">

                        <input type="hidden" name="login_by" id="login_by" value="{{ $provider_details->login_by ?: 'manual'}}">
                        <h4>{{tr('personal_details')}}</h4><hr>
                        <div class="row">

                            <div class="form-group col-md-6">
                                <label for="name">{{ tr('name') }} <span class="admin-required">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ tr('name') }}" value="{{ old('name') ?: $provider_details->name}}" required> 

                            </div>

                           
                            <div class="form-group col-md-6">

                                <label for="email">{{ tr('email')}} <span class="admin-required">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ tr('email')}}" value="{{ old('email') ?: $provider_details->email}}" required>
                            </div>

                        </div>

                        <div class="row">

                             <div class="form-group col-md-6">
                                <label for="mobile">{{ tr('mobile') }} <span class="admin-required">*</span></label>

                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="{{ tr('mobile') }}" value="{{ old('mobile') ?: $provider_details->mobile}}" required>
                            </div>

                             @if(!$provider_details->id)

                                <div class="form-group col-md-6">
                                    <label for="password">{{ tr('password') }} <span class="admin-required">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ tr('password') }}" required >
                                </div>

                            @endif

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">
                                <label for="work">{{ tr('work')}}</label>
                                <input type="work" class="form-control" id="work" name="work" placeholder="{{ tr('work')}}" value="{{ old('work') ?: $provider_details->work}}">
                            </div> 

                            <div class="form-group col-md-6">
                                <label for="school">{{ tr('school')}}</label>
                                <input type="school" class="form-control" id="school" name="school" placeholder="{{ tr('school')}}" value="{{ old('school') ?: $provider_details->school}}">
                            </div>

                        </div>

                        <div class="row">
                        
                            <div class="form-group col-md-6">
                                <label for="full_address">{{ tr('full_address')}}</label>
                                <input type="full_address" class="form-control" id="full_address" name="full_address" placeholder="{{ tr('full_address')}}" value="{{ old('full_address') ?: $provider_details->full_address}}">
                            </div> 
                            
                            <div class="form-group col-md-6">
                                <label for="languages">{{ tr('languages')}}</label>
                                <input type="languages" class="form-control" id="languages" name="languages" placeholder="{{ tr('languages')}}" value="{{old('languages') ?: $provider_details->languages}}" required>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label>{{tr('upload_image')}}</label>

                                <input type="file" name="picture" class="file-upload-default" accept="image/*">

                                <div class="input-group col-xs-12">

                                    <input type="file" name="picture" class="form-control file-upload-info" placeholder="{{tr('upload_image')}}" accept="image/*">

                                    <div class="input-group-append">
                                        <button class="btn btn-info" type="button">{{tr('upload')}}</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <h4>{{tr('account_details')}}</h4><hr>
                        <div class="row">
                        
                            <div class="form-group col-md-6">
                                <label for="account_name">{{ tr('account_name')}}</label>
                                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="{{ tr('account_name')}}" value="{{$provider_billing_info->account_name ?? ''}}">
                            </div> 
                            
                            <div class="form-group col-md-6">
                                <label for="paypal_email">{{ tr('paypal_email')}}</label>
                                <input type="email" class="form-control" id="paypal_email" name="paypal_email" placeholder="{{ tr('paypal_email')}}" value="{{$provider_billing_info->paypal_email ?? ''}}">
                            </div>

                        </div>

                        <div class="row">
                        
                            <div class="form-group col-md-6">
                                <label for="account_no">{{ tr('account_no')}}</label>
                                <input type="text" class="form-control" id="account_no" name="account_no" placeholder="{{ tr('account_no')}}" value="{{$provider_billing_info->account_no ?? ''}}">
                            </div> 
                            
                            <div class="form-group col-md-6">
                                <label for="route_no">{{ tr('route_no')}}</label>
                                <input type="text" class="form-control" id="route_no" name="route_no" placeholder="{{ tr('route_no')}}" value="{{$provider_billing_info->route_no ?? ''}}">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="description">{{ tr('description') }} </label>
                                <textarea class="form-control" id="description" name="description" >{{ old('description') ?: $provider_details->description}}</textarea>
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