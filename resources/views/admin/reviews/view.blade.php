@extends('layouts.admin') 

@section('title', tr('reviews'))

@section('breadcrumb')

    @if($sub_page == 'reviews-provider')

        <li class="breadcrumb-item"><a href=" {{ route('admin.reviews.providers')}}">   {{tr('review')}}</a>
        </li>

        <li class="breadcrumb-item active" aria-current="page">
            <span>{{tr('provider_reviews')}}</span>
        </li>

    @else

        <li class="breadcrumb-item"><a href=" {{ route('admin.reviews.users') }}">   {{tr('review')}}</a>
        </li>

        <li class="breadcrumb-item active" aria-current="page">
            <span>{{tr('user_reviews')}}</span>
        </li>

    @endif     

@endsection  

@section('styles')

<!-- <link rel="stylesheet" href="{{asset('admin-assets/css/star-rating.css')}}"> -->

<link rel="stylesheet" type="text/css" href="{{asset('admin-assets/css/star-rating-svg.css')}}">

@endsection

@section('content')
    
    <div class="row">

        <div class="col-md-12">

            <!-- Card group -->
            <div class="card-group">

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        <h4 class="card-title">{{ tr('rating') }}</h4>
                        <div class="my-rating"></div>

                        <!-- Title -->
                        <h4 class="card-title">{{ tr('review') }}</h4>
                        <!-- Text -->
                        <p class="card-text">{{ $review_details->review }}</p>
                        
                    </div>
                    <!-- Card content -->

                </div>

                <!-- Card -->
                <div class="card mb-4">

                    <!-- Card content -->
                    <div class="card-body">

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('user_name')}}</h5>
                            
                            <p class="card-text">
                                <a href="{{ route('admin.users.view', ['user_id' => $review_details->userDetails->id ?? '0' ] ) }}">{{ $review_details->userDetails->name ?? tr('user_not_avail') }} </a>
                            </p>

                        </div> 

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('provider_name')}}</h5>
                            
                            <p class="card-text">
                            
                            <a href="{{ route('admin.providers.view', ['provider_id' => $review_details->providerDetails->id ?? '0'] ) }}">{{ $review_details->providerDetails->name ?? tr('provider_not_avail') }} </a>
                            </p>

                        </div>

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('host_name')}}</h5>
                            
                            <p class="card-text">

                                <a href="{{ route('admin.spaces.view', ['host_id' => $review_details->host_id ?? '0'] ) }}"> {{ $review_details->hostDetails->host_name ?? tr('host_not_avail') }}  </a>

                            </p>

                        </div>
                                                
                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('updated_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($review_details->updated_at) }}</p>

                        </div>

                        <div class="custom-card">
                        
                            <h5 class="card-title">{{tr('created_at')}}</h5>
                            
                            <p class="card-text">{{ common_date($review_details->created_at) }}</p>

                        </div> 

                    </div>
                    <!-- Card content -->

                </div>
                <!-- Card -->
            
            </div>
            <!-- Card group -->

        </div>

    </div>

@endsection

@section('scripts')

    <script type="text/javascript" src="{{asset('admin-assets/js/jquery.star-rating-svg.min.js')}}"> </script>

    <script>
        $(".my-rating").starRating({
            starSize: 25,
            readOnly: true,
            initialRating: "{{$review_details->ratings}}",
            callback: function(currentRating, $el){
                // make a server call here
            }
        }); 
    </script>

@endsection
