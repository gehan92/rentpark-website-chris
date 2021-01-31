<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderRedeem extends Model
{
    /**
     * Get the provider details associated with booking
     */
    public function providerDetails() {

        return $this->belongsTo(Provider::class, 'provider_id');

    }
}
