<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderSubscriptionPayment extends Model
{
    public function providerDetails() {
    	return $this->belongsTo('App\Provider', 'provider_id');
    }

    public function providerSubscriptionDetails() {
    	return $this->belongsTo('App\ProviderSubscription', 'provider_subscription_id');
    }
   
}
