<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Setting;

class ProviderSubscription extends Model
{
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

    	$currency = Setting::get('currency') ?: "$";

        return $query->select('provider_subscriptions.id as provider_subscription_id', 
                'provider_subscriptions.title' , 
                'provider_subscriptions.description' , 
                'provider_subscriptions.picture' , 
                \DB::raw("'$currency' as currency"),
                'provider_subscriptions.amount' , 
                'provider_subscriptions.plan' , 
                'provider_subscriptions.plan_type' , 
                'provider_subscriptions.status'
                );
    }

    /**
     * Get the subscription payment record associated with the user.
     */
    public function subscriptionPayments() {

        return $this->hasMany(ProviderSubscriptionPayment::class);

    }

    /**
     * Get the bookings record associated with the user.
     */
    public function providerBookingPayments() {
        
        return $this->hasMany(ProviderSubscriptionPayment::class);
    }

}
