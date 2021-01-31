@extends('layouts.admin')

@section('title', tr('edit_document'))

@section('breadcrumb')
	
    <li class="breadcrumb-item"><a href="{{ route('admin.documents.index') }}">{{tr('documents')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">
    	<span>{{ tr('edit_document') }}</span>
    </li>
           
@endsection 

@section('content')

	@include('admin.documents._form')

@endsection