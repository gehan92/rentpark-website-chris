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
        <div class="card-header bg-card-header ">
            <h4 class="card-title">{{ tr('provider') }} {{ tr('documents') }}</h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">
              
                <table id="order-listing" class="table">
                    
                    <thead>
                        <tr>
                            <th>{{ tr('id') }}</th>
                            <th>{{ tr('provider') }}</th>
                            <th>{{ tr('documents') }}</th>
                            <th>{{ tr('updated_on') }}</th>
                            <th>{{ tr('status') }}</th>
                            <th>{{ tr('file') }}</th>
                            <th>{{ tr('action') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($provider_documents as $index => $provider_document_details)

                        <tr>

                            <td>{{ showEntries($_GET,$index+1) }}</td>

                            <td>
                                <a href="{{ route('admin.providers.view', ['provider_id' => $provider_document_details->provider_id]) }}">{{ $provider_document_details->providerDetails->name ??  "-" }}</a>
                            </td>
                            <td>
                                <a href="{{ route('admin.documents.view',['document_id' => $provider_document_details->document_id ]) }}">{{ $provider_document_details->documentDetails->name ?? "-" }}</a>
                            </td>
                            <td>
                                {{ common_date($provider_document_details->updated_at) }}
                            </td>
                          
                            <td>
                                @if($provider_document_details->status == APPROVED)

                                    <span class="badge badge-outline-success">{{ tr('approved') }} </span>

                                @else

                                    <span class="badge badge-outline-danger">{{ tr('declined') }} </span>

                                @endif
                            </td>

                            <td>
                                <a href="{{ $provider_document_details->document_url ? $provider_document_details->document_url : " - " }}" target="_blank"><span class="btn btn-info btn-large">{{ tr('view') }}</span>
                                </a>
                            </td>

                            <td>
                                @if($provider_document_details->status == APPROVED)
                                    <a href="{{ route('admin.providers.documents.status', ['provider_document_id' => $provider_document_details->id]) }}" onclick="return confirm(&quot;{{tr('provider_document_decline_confirmation')}}&quot;);" class="btn btn-danger btn-large">
                                        {{ tr('decline') }} 
                                    </a>

                                @else
                                    
                                    <a class="btn btn-success btn-large" href="{{ route('admin.providers.documents.status', ['provider_document_id' => $provider_document_details->id]) }}">
                                        {{ tr('approve') }} 
                                    </a>
                                       
                                @endif
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