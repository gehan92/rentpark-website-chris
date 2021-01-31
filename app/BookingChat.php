<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingChat extends Model
{
    /**
     * Get the user details associated with booking
     */
	public function userDetails() {

		return $this->belongsTo(User::class);

	}

	/**
     * Get the provider details associated with booking
     */

	public function providerDetails() {

		return $this->belongsTo(Provider::class, 'provider_id');

	}

	/**
     * Get the host details associated with booking
     */

	public function bookingDetails() {

		return $this->belongsTo(Booking::class);

	}
}
