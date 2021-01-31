<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                <form class="forms-sample" action="{{ Setting::get('is_demo_control_enabled') == NO ? route('admin.users.save') : '#'}}" method="POST" enctype="multipart/form-data" role="form">

                @csrf

                    <div class="card-header bg-card-header">

                        <h4 class="">{{tr('user')}}
                            <a class="btn btn-secondary pull-right" href="{{route('admin.users.index')}}">
                                <i class="fa fa-eye"></i> {{tr('view_users')}}
                            </a>
                            
                        </h4>

                    </div>

                    <div class="card-body">

                        @if($user_details->id)

                            <input type="hidden" name="user_id" id="user_id" value="{{$user_details->id}}">

                        @endif

                        <input type="hidden" name="login_by" id="login_by" value="{{$user_details->login_by ?: 'manual'}}">

                        <input type="hidden" name="billing_info_id" id="billing_info_id" value="{{$user_billing_info->id ?? ''}}">

                        <h4>{{tr('personal_details')}}</h4><hr>
                        <div class="row">
                           
                            <div class="form-group col-md-6">
                                <label for="name">{{ tr('name') }} <span class="admin-required">*</span> </label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ tr('name') }}" value="{{ old('name') ?: $user_details->name}}" required>

                            </div>

                            <div class="form-group col-md-6">

                                <label for="mobile">{{ tr('mobile') }}  </label>

                                <input type="number" class="form-control" pattern="[0-9]{6,13}" id="mobile" name="mobile" placeholder="{{ tr('mobile') }}" value="{{ old('mobile') ?: $user_details->mobile}}">
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">
                                <label for="email">{{ tr('email')}} <span class="admin-required">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ tr('email')}}" value="{{ old('email') ?: $user_details->email}}" required>
                            </div>

                            @if(!$user_details->id)

                                <div class="form-group col-md-6">
                                    <label for="password">{{ tr('password') }} <span class="admin-required">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ tr('password') }}" value="{{old('password')}}" required title="{{ tr('password_notes') }}">
                                </div>

                            @endif

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label>{{tr('upload_image')}}</label>

                                <input type="file" name="picture" class="file-upload-default"  accept="image/*">

                                <div class="input-group col-xs-12">

                                    <input type="file" class="form-control file-upload-info" name="picture" placeholder="{{tr('upload_image')}}">

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
                                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="{{ tr('account_name')}}" value="{{$user_billing_info->account_name ?? ''}}">
                            </div> 
                            
                            <div class="form-group col-md-6">
                                <label for="paypal_email">{{ tr('paypal_email')}}</label>
                                <input type="email" class="form-control" id="paypal_email" name="paypal_email" placeholder="{{ tr('paypal_email')}}" value="{{$user_billing_info->paypal_email ?? ''}}">
                            </div>

                        </div>

                        <div class="row">
                        
                            <div class="form-group col-md-6">
                                <label for="account_no">{{ tr('account_no')}}</label>
                                <input type="number" class="form-control" id="account_no" name="account_no" placeholder="{{ tr('account_no')}}" value="{{$user_billing_info->account_no ?? ''}}">
                            </div> 
                            
                            <div class="form-group col-md-6">
                                <label for="route_no">{{ tr('route_no')}}</label>
                                <input type="text" class="form-control" id="route_no" name="route_no" placeholder="{{ tr('route_no')}}" value="{{$user_billing_info->route_no ?? ''}}">
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-12">

                                <label for="simpleMde">{{ tr('description') }}</label>

                                <textarea class="form-control" id="description" name="description">{{ old('description') ?: $user_details->description}}</textarea>

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