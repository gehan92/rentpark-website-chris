@extends('layouts.admin')

@section('title', tr('edit_vehicle_details'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.users.view', ['user_id' => $vehicle_details->user_id]) }}">{{tr('view_user')}}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('edit_vehicle_details')}}</span>
    </li>
           
@endsection

@section('content')

	@include('admin.vehicle_details._form')

@endsection