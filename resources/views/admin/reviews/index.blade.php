@extends('layouts.admin') 

@section('title', tr('reviews'))

@section('breadcrumb')

    <li class="breadcrumb-item" aria-current="page">
    	<a href="javascript:void(0)">{{tr('reviews')}}</a>
    </li>

    @if($sub_page == 'reviews-user')
    <li class="breadcrumb-item active">{{tr('user_reviews')}}</li>
    @else
    <li class="breadcrumb-item active">{{tr('provider_reviews')}}</li>
    @endif
         
@endsection 

@section('styles')

<link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/star-rating-svg.css')}}">

@endsection

@section('content') 

<div class="col-lg-12 grid-margin stretch-card">
        
    <div class="card">

        <div class="card-header bg-card-header ">
        
        @if($sub_page == 'reviews-user')
            <h4 class="">{{ tr('user_reviews') }}</h4>
        @else
            <h4 class="">{{ tr('provider_reviews') }}</h4>
        @endif
        
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="order-listing" class="table">

                    <thead>
                        <th>{{ tr('s_no') }}</th>
                        <th>{{ tr('user') }}</th>
                        <th>{{ tr('provider') }}</th>
                        <th>{{ tr('date') }}</th>
                        <th>{{ tr('rating') }}</th>
                        <th>{{ tr('comment') }}</th>
                        <th>{{ tr('action') }}</th>
                    </thead>

                    <tbody>
                     
                        @foreach($reviews as $i => $review_details)

                            <tr>
                                <td>{{ $i+1 }}</td>
                               
                                <td>

                                    <a href="{{ route('admin.users.view', ['user_id' => $review_details->userDetails->id ?? '0' ] ) }}">
                                        {{ $review_details->userDetails->name ?? tr('user_not_avail') }}
                                    </a>
                                </td>

                                <td>

                                    <a href="{{route('admin.providers.view', ['provider_id' => $review_details->providerDetails->id ?? '0' ])}}">

                                        {{$review_details->providerDetails->name ?? tr('provider_not_avail')}}
                                    </a>
                                </td>
                                
                                <td>
                                    {{ common_date($review_details->created_at) }}
                                </td>

                                <td>
                                    <div class="my-rating-{{$i}}"></div>
                                </td> 
                                
                                <td>{{ substr($review_details->review, 0, 50) }}...</td>

                                <td>
                                
                                    @if($sub_page == 'reviews-user')
                                   
                                        <a class="btn btn-outline-primary" href="{{ route('admin.reviews.users.view', ['booking_review_id' => $review_details->id])}}">{{tr('view')}}</a> 
                                   
                                    @else
                                       
                                        <a class="btn btn-outline-primary" href="{{ route('admin.reviews.providers.view', ['booking_review_id' => $review_details->id])}}">{{tr('view')}}</a> 

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

@section('scripts')

     <script type="text/javascript" src="{{asset('admin-assets/js/jquery.star-rating-svg.min.js')}}"> </script>

    <script>
        <?php foreach ($reviews as $i => $review_details) { ?>
            $(".my-rating-{{$i}}").starRating({
                starSize: 25,
                initialRating: "{{$review_details->ratings}}",
                readOnly: true,
                callback: function(currentRating, $el){
                    // make a server call here
                }
            });
        <?php } ?>
    </script>

@endsection

