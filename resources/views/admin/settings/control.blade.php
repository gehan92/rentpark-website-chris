@extends('layouts.admin') 

@section('title', tr('admin_control'))

@section('breadcrumb')

<li class="breadcrumb-item active" aria-current="page">

    <span>{{ tr('admin_control') }}</span>
</li>

@endsection

@section('content')


<div class="col-lg-12 grid-margin stretch-card">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                <form class="forms-sample" action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" role="form">

                    @csrf

                    <div class="card-header bg-card-header ">

                        <h4 class="">{{tr('admin_control')}}</h4>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('is_demo_control_enabled') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_demo_control_enabled" value="1" class="flat-red" @if(Setting::get('is_demo_control_enabled') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_demo_control_enabled" class="flat-red"  value="0" @if(Setting::get('is_demo_control_enabled') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('is_account_email_verification') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_account_email_verification" value="1" class="flat-red" @if(Setting::get('is_account_email_verification') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_account_email_verification" class="flat-red"  value="0" @if(Setting::get('is_account_email_verification') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('is_email_notification') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_email_notification" value="1" class="flat-red" @if(Setting::get('is_email_notification') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_email_notification" class="flat-red"  value="0" @if(Setting::get('is_email_notification') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('is_email_configured') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_email_configured" value="1" class="flat-red" @if(Setting::get('is_email_configured') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_email_configured" class="flat-red"  value="0" @if(Setting::get('is_email_configured') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('is_push_notification') }}</label>
                                <br>
                                <label>
                                    <input required type="radio" name="is_push_notification" value="1" class="flat-red" @if(Setting::get('is_push_notification') == 1) checked @endif>
                                    {{tr('yes')}}
                                </label>

                                <label>
                                    <input required type="radio" name="is_push_notification" class="flat-red"  value="0" @if(Setting::get('is_push_notification') == 0) checked @endif>
                                    {{tr('no')}}
                                </label>
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('admin_take_count') }}</label>
                                
                                <input type="number" name="admin_take_count" class="form-control" value="{{Setting::get('admin_take_count', 6)}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('currency') }}</label>
                                
                                <input type="text" name="currency" class="form-control" value="{{Setting::get('currency', '$')}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('currency_code') }}</label>
                                
                                <input type="text" name="currency_code" class="form-control" value="{{Setting::get('currency_code', 'USD')}}">
                        
                            </div>

                        </div>

                        <div class="clearfix"></div>

                        <div class="row">

                            <div class="col-md-12">

                                <hr><h4>Demo Login Details</h4><hr>

                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('demo_admin_email') }}</label>
                                
                                <input type="text" name="demo_admin_email" class="form-control" value="{{Setting::get('demo_admin_email')}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('demo_admin_password') }}</label>
                                
                                <input type="text" name="demo_admin_password" class="form-control" value="{{Setting::get('demo_admin_password')}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('demo_provider_email') }}</label>
                                
                                <input type="text" name="demo_provider_email" class="form-control" value="{{Setting::get('demo_provider_email')}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('demo_provider_password') }}</label>
                                
                                <input type="text" name="demo_provider_password" class="form-control" value="{{Setting::get('demo_provider_password')}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('demo_user_email') }}</label>
                                
                                <input type="text" name="demo_user_email" class="form-control" value="{{Setting::get('demo_user_email')}}">
                        
                            </div>

                            <div class="form-group col-md-6">
                                           
                                <label>{{ tr('demo_user_password') }}</label>
                                
                                <input type="text" name="demo_user_password" class="form-control" value="{{Setting::get('demo_user_password')}}">
                        
                            </div>
                        
                        </div>
                    
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success mr-2">{{ tr('submit') }}</button>
                    </div>

                </form>

            </div>

        </div>
    
    </div>

</div>
@endsection


@section('scripts')


@endsection