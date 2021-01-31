@extends('layouts.admin') 

@section('title', tr('add_amenity'))

@section('breadcrumb')

    <li class="breadcrumb-item">
    	<a href="{{ route('admin.amenities.index') }}">{{tr('amenities')}}</a>
    </li>
    
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{tr('add_amenity')}}</span>
    </li>
           
@endsection 

@section('content')
	
	@include('admin.amenities._form') 

@endsection