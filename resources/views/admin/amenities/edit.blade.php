@extends('layouts.admin')

@section('title', tr('edit_amenity'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.amenities.index') }}">{{tr('amenities')}}</a></li>
    
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{ tr('edit_amenity') }}</span>
    </li>
           
@endsection 

@section('content')

	@include('admin.amenities._form')

@endsection