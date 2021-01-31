@extends('layouts.admin')

@section('title', tr('documents'))

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{ route('admin.providers.index') }}">{{ tr('providers') }}</a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('documents') }}</span>
</li>

@endsection 

@section('content') 

<div class="col-lg-12 grid-margin stretch-card">
    
    <div class="card">
        
        <div class="card-header bg-card-header">

            <h4 class="">{{tr('documents')}}
                <a class="btn btn-secondary pull-right" href="{{route('admin.providers.index')}}">
                    <i class="fa fa-eye"></i> {{tr('view_providers')}}
                </a>
                
            </h4>

        </div>
    
        <div class="card-body">


            <div class="table-responsive">
              
                <table id="order-listing" class="table">
                    
                    <thead>
                        <tr>
                            <th>{{ tr('id') }}</th>
                            <th>{{ tr('provider') }}</th>
                            <th>{{ tr('documents') }}</th>                            
                            <th>{{ tr('action') }}</th>                            
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($provider_documents as $index => $provider_document_details)

                        <tr>

                            <td>{{ $index+1 }}</td>

                            <td>
                                <a href="{{ route('admin.providers.view', ['provider_id' => $provider_document_details->provider_id]) }}">{{ $provider_document_details->providerDetails->name ??  "-" }}</a>
                            </td>

                            <td>
                                <a href="{{ route('admin.documents.view',['document_id' => $provider_document_details->document_id ]) }}">{{ $provider_document_details->documentDetails->name ?? "-" }}</a>
                            </td>

                            <td>
                                <a href="{{ route('admin.providers.documents.view',['provider_id' => $provider_document_details->provider_id ]) }}"><span class="btn btn-info btn-large">{{ tr('view') }}</span>
                                </a>
                            </td>
                            
                        </tr>

                        @endforeach

                    </tbody>

                </table>
            
            </div>
        
        </div>
    
    </div>

</div>

@endsection