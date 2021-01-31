<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
       
        <li class="nav-item" id="dashboard" >
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="icon-rocket menu-icon"></i>
                <span class="menu-title">{{ tr('dashboard')}}</span>
            </a>
        </li>

        <li class="nav-item" id="users">
            <a class="nav-link" data-toggle="collapse" href="#users-sidebar" aria-expanded="false" aria-controls="users-sidebar">
                <i class="icon-user menu-icon"></i>
                <span class="menu-title">{{ tr('users') }}</span>
            </a>

            <div class="collapse" id="users-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="users-create" href="{{route('admin.users.create')}} "> {{ tr('add_user') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="users-view" href=" {{route('admin.users.index')}} "> {{ tr('view_users') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li> 

        <li class="nav-item" id="providers">
            <a class="nav-link" data-toggle="collapse" href="#providers-sidebar" aria-expanded="false" aria-controls="providers-sidebar">
                <i class="icon-people menu-icon"></i>
                <span class="menu-title">{{ tr('providers') }}</span>
            </a>

            <div class="collapse" id="providers-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="providers-create" href="{{route('admin.providers.create')}} "> {{ tr('add_provider') }} </a>
                    </li>
                   
                    <li class="nav-item"> 
                        <a class="nav-link" id="providers-view" href=" {{route('admin.providers.index')}} "> {{ tr('view_providers') }}  </a>
                    </li>  

                    <li class="nav-item"> 
                        <a class="nav-link" id="providers-documents" href=" {{route('admin.providers.documents.index')}} "> {{ tr('documents') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li>

        <li class="nav-item nav-item-header">

            <a class="nav-link background-color">
                <span class="menu-title text-uppercase">{{ tr('space')}} Management</span>
            </a>
            
        </li>

        <li class="nav-item" id="categories" style="display: none">
            <a class="nav-link" data-toggle="collapse" href="#categories-sidebar" aria-expanded="false" aria-controls="categories-sidebar">
                <i class="icon-list menu-icon"></i>
                <span class="menu-title">{{ tr('categories') }}</span>
            </a>

            <div class="collapse" id="categories-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="categories-create" href="{{route('admin.categories.create')}} "> {{ tr('add_category') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="categories-view" href=" {{route('admin.categories.index')}} "> {{ tr('view_categories') }} </a>
                    </li>                    
                </ul>
            </div>
        </li>        

        <li class="nav-item" id="amenities">
            <a class="nav-link" data-toggle="collapse" href="#amenities-sidebar" aria-expanded="false" aria-controls="amenities-sidebar">
                <i class="icon-list menu-icon"></i>
                <span class="menu-title">{{ tr('amenities') }}</span>
            </a>

            <div class="collapse" id="amenities-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="amenities-create" href="{{route('admin.amenities.create')}} "> {{ tr('add_amenity') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="amenities-view" href=" {{route('admin.amenities.index')}} "> {{ tr('view_amenities') }} </a>
                    </li>                    
                </ul>
            </div>
        </li>

        <!-- <li class="nav-item" id="sub_categories">
            <a class="nav-link" data-toggle="collapse" href="#sub_categories-sidebar" aria-expanded="false" aria-controls="sub_categories-sidebar">
                <i class="icon-layers menu-icon"></i>
                <span class="menu-title">{{ tr('sub_categories') }}</span>
            </a>

            <div class="collapse" id="sub_categories-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="sub_categories-create" href="{{route('admin.sub_categories.create')}} "> {{ tr('add_sub_category') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="sub_categories-view" href=" {{route('admin.sub_categories.index')}} "> {{ tr('view_sub_categories') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li> -->

        <li class="nav-item" id="questions" style="display: none;">
            <a class="nav-link" data-toggle="collapse" href="#questions-sidebar" aria-expanded="false" aria-controls="questions-sidebar">
                <i class="icon-question menu-icon"></i>
                <span class="menu-title">{{ tr('questions') }}</span>
            </a>

            <div class="collapse" id="questions-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="questions-create" href="{{route('admin.questions.create')}} "> {{ tr('add_question') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="questions-view" href=" {{route('admin.questions.index')}} "> {{ tr('view_questions') }}  </a>
                    </li>

                    <li class="nav-item" style="display: none;"> 
                        <a class="nav-link" id="question_groups-view" href=" {{route('admin.question_groups.index')}} "> {{ tr('view_question_groups') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li>

        <li class="nav-item" id="service-locations">

            <a class="nav-link" data-toggle="collapse" href="#service-locations-sidebar" aria-expanded="false" aria-controls="service-locations-sidebar">
                <i class="menu-icon fa fa-globe"></i>
                <span class="menu-title">{{ tr('service_locations') }}</span>
            </a>

            <div class="collapse" id="service-locations-sidebar">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="service-locations-create" href="{{route('admin.service_locations.create')}} "> {{ tr('add_service_location') }} </a>
                    </li>

                    <li class="nav-item"> 
                        <a class="nav-link" id="service-locations-view" href=" {{route('admin.service_locations.index')}} "> {{ tr('view_service_locations') }}  </a>
                    </li>
                </ul>
            </div>

        </li>

        <li class="nav-item" id="hosts">

            <a class="nav-link" data-toggle="collapse" href="#hosts-sidebar" aria-expanded="false" aria-controls="hosts-sidebar">
                <i class="icon-basket menu-icon"></i>
                <span class="menu-title">{{ tr('parking_space') }}</span>
            </a>

            <div class="collapse" id="hosts-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="hosts-create" href="{{route('admin.spaces.create')}} "> {{ tr('add_space') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="hosts-view" href=" {{route('admin.spaces.index')}} "> {{ tr('view_space') }}  </a>
                    </li>

                    <li class="nav-item"> 
                        <a class="nav-link" id="hosts-unverified" href=" {{route('admin.spaces.index' , ['unverified' => YES])}} "> {{ tr('unverified_spaces') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li>

        <li class="nav-item" id="provider_subscriptions">
            <a class="nav-link" data-toggle="collapse" href="#provider_subscriptions-sidebar" aria-expanded="false" aria-controls="provider_subscriptions-sidebar">
                <i class="icon-key menu-icon"></i>
                <span class="menu-title">{{ tr('provider_subscriptions') }}</span>
            </a>

            <div class="collapse" id="provider_subscriptions-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="provider_subscriptions-create" href="{{route('admin.provider_subscriptions.create')}} "> {{ tr('add_provider_subscription') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="provider_subscriptions-view" href=" {{route('admin.provider_subscriptions.index')}} "> {{ tr('view_provider_subscriptions') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li>

        <li class="nav-item nav-item-header">

            <a class="nav-link background-color">
                <span class="menu-title text-uppercase">Booking Management</span>
            </a>
            
        </li>

        <!-- <li class="nav-item" id="bookings">
           
            <a class="nav-link" data-toggle="collapse" href="#bookings-sidebar" aria-expanded="false" aria-controls="bookings-sidebar">
                <i class="icon-calendar menu-icon"></i>
                <span class="menu-title">{{ tr('bookings') }}</span>
            </a>

            <div class="collapse" id="bookings-sidebar">

                <ul class="nav flex-column sub-menu">

                    <li class="nav-item"> 
                        <a class="nav-link" id="bookings-index" href=" {{route('admin.bookings.index')}} "> {{ tr('bookings') }}  </a>
                    </li> 

                </ul>

            </div>

        </li>         -->

        <li class="nav-item" id="bookings">
            <a class="nav-link" data-toggle="collapse" href="#bookings-sidebar" aria-expanded="false" aria-controls="bookings-sidebar">
                <i class="icon-calendar menu-icon"></i>
                <span class="menu-title">{{ tr('bookings') }}</span>
            </a>

            <div class="collapse" id="bookings-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="bookings-dashboard" href="{{route('admin.bookings.dashboard')}} "> {{ tr('dashboard') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="bookings-view" href=" {{route('admin.bookings.index')}} "> {{ tr('history') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li>

        <li class="nav-item" id="revenues">

            <a class="nav-link" data-toggle="collapse" href="#revenues-sidebar" aria-expanded="false" aria-controls="revenues-sidebar">
                <i class="icon-credit-card menu-icon"></i>
                <span class="menu-title">{{ tr('revenues') }}</span>
            </a>

            <div class="collapse" id="revenues-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="revenues-dashboard" href="{{route('admin.revenues.dashboard')}} "> {{ tr('dashboard') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="revenues-payments" href=" {{route('admin.bookings.payments')}} "> {{ tr('bookings_payments') }}  </a>
                    </li> 

                    <li class="nav-item"> 
                        <a class="nav-link" id="revenues-provider_subscription-payments" href=" {{route('admin.provider_subscriptions.payments')}} "> {{ tr('subscription_payments') }}  </a>
                    </li>

                    <li class="nav-item"> 
                        <a class="nav-link" id="revenues-provider_redeems" href=" {{route('admin.provider_redeems.index')}} "> {{ tr('provider_redeems') }}  </a>
                    </li> 

                    <li class="nav-item"> 
                        <a class="nav-link" id="revenues-user_refunds" href=" {{route('admin.user_refunds.index')}} "> {{ tr('user_refunds') }}  </a>
                    </li> 
                                       
                </ul>

            </div>

        </li>

        <li class="nav-item" id="reviews">

            <a class="nav-link" data-toggle="collapse" href="#reviews-sidebar" aria-expanded="false" aria-controls="reviews-sidebar">
                <i class="icon-star menu-icon"></i>
                <span class="menu-title">{{ tr('reviews') }}</span>
            </a>

            <div class="collapse" id="reviews-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="reviews-user" href="{{route('admin.reviews.users')}} "> {{ tr('user_reviews') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="reviews-provider" href=" {{route('admin.reviews.providers')}} "> {{ tr('provider_reviews') }}  </a>
                    </li>                    
                </ul>
            </div>

        </li>

        <li class="nav-item nav-item-header">

            <a class="nav-link background-color">
                <span class="menu-title text-uppercase">Settings Management</span>
            </a>
            
        </li>

        <li class="nav-item" id="settings">
            <a class="nav-link" href="{{ route('admin.settings') }}" id="settings-view">
                <i class="icon-settings menu-icon"></i>
                <span class="menu-title">{{ tr('settings') }}</span>
            </a>
        </li>

        <li class="nav-item" id="static_pages">
            <a class="nav-link" data-toggle="collapse" href="#static_pages-sidebar" aria-expanded="false" aria-controls="static_pages-sidebar">
                <i class="icon-bubbles menu-icon"></i>
                <span class="menu-title">{{ tr('static_pages') }}</span>
            </a>

            <div class="collapse" id="static_pages-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="static_pages-create" href="{{route('admin.static_pages.create')}} "> {{ tr('add_static_page') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="static_pages-view" href=" {{route('admin.static_pages.index')}} "> {{ tr('view_static_pages') }}  </a>
                    </li>                    
                </ul>

            </div>

        </li> 

        <li class="nav-item" id="documents">
            <a class="nav-link" data-toggle="collapse" href="#documents-sidebar" aria-expanded="false" aria-controls="documents-sidebar">
                <i class="icon-book-open menu-icon"></i>
                <span class="menu-title">{{ tr('documents') }}</span>
            </a>

            <div class="collapse" id="documents-sidebar">

                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" id="documents-create" href="{{route('admin.documents.create')}} "> {{ tr('add_document') }} </a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" id="documents-view" href=" {{route('admin.documents.index')}} "> {{ tr('view_documents') }}  </a>
                    </li>                    
                </ul>

            </div>

        </li>

        <li class="nav-item" id="help">
            <a class="nav-link" href="{{ route('admin.help') }}">
                <i class="icon-directions menu-icon"></i>
                <span class="menu-title">{{ tr('help') }}</span>
            </a>
        </li>

        <li class="nav-item" id="logout">
            <a class="nav-link" href="{{ route('admin.logout') }}">
                <i class="fa fa-power-off menu-icon"></i>
                <span class="menu-title">{{ tr('logout') }}</span>
            </a>
        </li>

    </ul>
</nav>