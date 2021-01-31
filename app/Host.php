<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class Host extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        $currency = \Setting::get('currency' , '$');

        return $query->where('hosts.status' , HOST_OWNER_PUBLISHED)
            ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
            ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED)
            ->leftJoin('providers','providers.id' ,'=' , 'hosts.provider_id')
            ->leftJoin('categories','categories.id' ,'=' , 'hosts.category_id')
            ->leftJoin('sub_categories','sub_categories.id' ,'=' , 'hosts.sub_category_id')
            ->select(
            'hosts.id as host_id',
            'hosts.host_name as host_name',
            'hosts.picture as host_picture',
            'hosts.overall_ratings',
            'hosts.total_ratings',
            'hosts.provider_id as provider_id',
             \DB::raw('IFNULL(providers.name,"") as provider_name'),
             \DB::raw('IFNULL(providers.picture,"") as provider_picture'),
            'hosts.category_id',
             \DB::raw('IFNULL(categories.name,"") as category_name'),
            'hosts.sub_category_id',
             \DB::raw('IFNULL(sub_categories.name,"") as sub_category_name'),
            'hosts.city',
            'hosts.base_price as base_price',
            \DB::raw("'$currency' as currency"),
            'hosts.created_at',
            'hosts.updated_at', // @todo check and remove the dates
            \DB::raw("DATE_FORMAT(hosts.created_at, '%M %Y') as created") ,
            \DB::raw("DATE_FORMAT(hosts.updated_at, '%M %Y') as updated")
            );

    }

    public function scopeVerifedHostQuery($query) {

        return $query->where('hosts.status' , HOST_OWNER_PUBLISHED)
            ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
            ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED);

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserParkResponse($query) {

        $currency = \Setting::get('currency' , '$');

        return $query->VerifedHostQuery()
            ->leftJoin('providers','providers.id' ,'=' , 'hosts.provider_id')
            // ->leftJoin('sub_categories','sub_categories.id' ,'=' , 'hosts.sub_category_id')
            ->select(
            'hosts.id as host_id',
            'hosts.unique_id as host_unique_id',
            'hosts.host_name as host_name',
            'hosts.picture as host_picture',
            'hosts.host_type',
            'hosts.overall_ratings',
            'hosts.total_ratings',
            'hosts.provider_id as provider_id',
            'hosts.city as host_location',
            'hosts.latitude',
            'hosts.longitude',
            'hosts.per_hour as per_hour',
            \DB::raw("'$currency' as currency")
            );

    }

        /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserParkFullResponse($query) {

        $currency = \Setting::get('currency' , '$');

        return $query->leftJoin('providers','providers.id' ,'=' , 'hosts.provider_id')
            ->select(
            'hosts.id as host_id',
            'hosts.unique_id as host_unique_id',
            'hosts.host_name as host_name',
            'hosts.description as host_description',
            'hosts.picture as host_picture',
            'hosts.overall_ratings',
            'hosts.total_ratings',
            'hosts.total_spaces as total_spaces',
            'hosts.host_type as host_type',

           /* 'hosts.access_note as access_note',
            'hosts.access_method as access_method',*/
            'hosts.host_owner_type as host_owner_type',

            'hosts.provider_id as provider_id',
            \DB::raw('IFNULL(providers.name,"") as provider_name'),
             \DB::raw('IFNULL(providers.picture,"") as provider_picture'),

            'hosts.amenities',

            'hosts.city as host_location',
            'hosts.latitude',
            'hosts.longitude',
            'hosts.per_hour as per_hour',
            'hosts.per_day as per_day',
            'hosts.per_week as per_week',
            'hosts.per_month as per_month',
            \DB::raw("'$currency' as currency"),
            \DB::raw("DATE_FORMAT(hosts.created_at, '%M %Y') as created") ,
            \DB::raw("DATE_FORMAT(hosts.updated_at, '%M %Y') as updated")
            );

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProviderParkFullResponse($query) {

        $currency = \Setting::get('currency' , '$');

        return $query->leftJoin('providers','providers.id' ,'=' , 'hosts.provider_id')
            ->select(
            'hosts.id as host_id',
            'hosts.unique_id as host_unique_id',
            'hosts.host_name as host_name',
            'hosts.description as host_description',
            'hosts.picture as host_picture',
            'hosts.host_type',
            'hosts.available_days',

            'hosts.overall_ratings',
            'hosts.total_ratings',
            'hosts.total_spaces as total_spaces',
            'hosts.amenities as amenities',

           /* 'hosts.access_note as access_note',
            'hosts.access_method as access_method',*/
            'hosts.security_code as security_code',
            'hosts.host_owner_type as host_owner_type',
            'hosts.provider_id as provider_id',

            \DB::raw('IFNULL(providers.name,"") as provider_name'),
             \DB::raw('IFNULL(providers.picture,"") as provider_picture'),
            'hosts.service_location_id',
            'hosts.latitude',
            'hosts.longitude',
            'hosts.full_address',
            'hosts.street_details',
            'hosts.city',
            'hosts.state',
            'hosts.country',
            'hosts.zipcode',

            'hosts.per_hour as per_hour',
            'hosts.per_day as per_day',
            'hosts.per_week as per_week',
            'hosts.per_month as per_month',
            \DB::raw("'$currency' as currency"),
            \DB::raw("DATE_FORMAT(hosts.created_at, '%M %Y') as created") ,
            \DB::raw("DATE_FORMAT(hosts.updated_at, '%M %Y') as updated")
            );

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserBaseResponse($query) {

        $currency = \Setting::get('currency' , '$');

        return $query->where('hosts.status' , HOST_OWNER_PUBLISHED)
            ->where('hosts.admin_status' , ADMIN_HOST_APPROVED)
            ->where('hosts.is_admin_verified' , ADMIN_HOST_VERIFIED)
            ->leftJoin('providers','providers.id' ,'=' , 'hosts.provider_id')
            ->leftJoin('sub_categories','sub_categories.id' ,'=' , 'hosts.sub_category_id')
            ->select(
            'hosts.id as host_id',
            'hosts.unique_id as host_unique_id',
            'hosts.host_name as host_name',
            'hosts.picture as host_picture',
            'hosts.overall_ratings',
            'hosts.total_ratings',
            'hosts.provider_id as provider_id',
            'sub_categories.name as sub_category_name',
            'hosts.service_location_id',
            'hosts.city as host_location',
            'hosts.latitude',
            'hosts.longitude',
            'hosts.base_price as base_price',
            'hosts.per_day as per_day',
            'hosts.per_hour as per_hour',
            \DB::raw("'$currency' as currency")
            );

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSingleBaseResponse($query) {

        $currency = \Setting::get('currency' , '$');

        return $query
            ->leftJoin('categories','categories.id' ,'=' , 'hosts.category_id')
            ->leftJoin('sub_categories','sub_categories.id' ,'=' , 'hosts.sub_category_id')
            ->leftJoin('host_details','host_details.host_id' ,'=' , 'hosts.id')
            ->select(
            'hosts.id as host_id',
            'hosts.unique_id as host_unique_id',
            'hosts.provider_id as provider_id',
            'hosts.host_name as host_name',
            'hosts.host_type as host_type',
            'hosts.description as host_description',
            'hosts.category_id as category_id',
            'categories.name as category_name',
            'hosts.sub_category_id as sub_category_id',
            'sub_categories.name as sub_category_name',
            'hosts.city as host_location',
            'hosts.picture as host_picture',
            'hosts.overall_ratings',
            'hosts.total_ratings',
            'hosts.latitude',
            'hosts.longitude',
            'hosts.checkin',
            'hosts.checkout',
           /* 'hosts.access_method',*/
            'host_details.min_guests',
            'host_details.max_guests',
            'hosts.min_days',
            'hosts.max_days',
            \DB::raw("'$currency' as currency"),
            'hosts.per_day'
            );

    }

    public function hostAvailabilities() {
        return $this->hasMany(HostAvailability::class, 'host_id');
    }

    // Need to Discuss - hasOne
    public function hostDetails() {
        return $this->hasMany(HostDetails::class, 'host_id');
    }

    public function hostGalleries() {
        return $this->hasMany(HostGallery::class, 'host_id');
    }

    public function hostInventories() {
        return $this->hasMany(HostInventory::class, 'host_id');
    }

    public function hostWishlist() {
        return $this->hasMany(Wishlist::class, 'host_id');
    }

    /**
     * Get the host question answer record associated with the host.
     */
    public function hostQuestionAnswers() {
        return $this->hasMany(HostQuestionAnswer::class, 'host_id');
    }

    /**
     * Get the booking record associated with the host.
     */
    public function bookings() {
        return $this->hasMany(Booking::class, 'host_id');
    }

    /**
     * Get the booking user review record associated with the host.
     */
    public function bookingUserReviews() {
        return $this->hasMany(BookingUserReview::class, 'host_id');
    }

    /**
     * Get the booking payments record associated with the host.
     */
    public function bookingPayments() {

        return $this->hasMany(BookingPayment::class, 'host_id');
    }

    /**
     * Get the provider record associated with the host.
     */
    public function providerDetails() {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    /**
     * Get the serviceLocation record associated with the host.
     */
    public function serviceLocationDetails() {
        return $this->belongsTo(ServiceLocation::class, 'service_location_id');
    }

    /**
     * Get the category record associated with the host.
     */
    public function categoryDetails() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the subCategory record associated with the host.
     */
    public function subCategoryDetails() {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Get the wishlists record associated with the host.
     */
    public function wishlists() {
        return $this->hasMany(Wishlist::class, 'host_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = routefreestring(isset($model->attributes['host_name']) ? $model->attributes['host_name'] : uniqid());

        });

        static::updating(function($model) {

            $model->attributes['unique_id'] = routefreestring(isset($model->attributes['host_name']) ? $model->attributes['host_name'] : uniqid());

        });

        static::deleting(function($model) {

            $model->hostAvailabilities()->delete();

            foreach ($model->hostGalleries as $key => $host_gallery_details) {

                Helper::delete_file($host_gallery_details->picture , FILE_PATH_HOST);

                $host_gallery_details->delete();

            }

            $model->wishlists()->delete();

            $model->hostInventories()->delete();

            $model->hostQuestionAnswers()->delete();

            foreach ($model->bookings as $key => $bookid_details) {

                $bookid_details->delete();
            }

            $model->hostWishlist()->delete();

        });
    }
}
