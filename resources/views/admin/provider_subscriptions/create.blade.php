@extends('layouts.admin') 

@section('title', tr('add_provider_subscription'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.provider_subscriptions.index') }}">{{tr('provider_subscriptions')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('add_provider_subscription')}}</span>
    </li>
           
@endsection 

@section('content') 

	@include('admin.provider_subscriptions._form') 

@endsection