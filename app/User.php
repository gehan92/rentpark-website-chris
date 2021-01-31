<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Setting, DB;

use App\Helpers\Helper;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        $is_appstore_updated = \Setting::get('is_appstore_updated' , NO);

        return $query->select(
            'users.id as user_id',
            'users.username as username',
            'users.name',
            'users.email as email',
            'users.picture as picture',
            'users.description as description',
            'users.mobile as mobile',
            'users.token as token',
            'users.token_expiry as token_expiry',
            'users.social_unique_id as social_unique_id',
            'users.login_by as login_by',
            'users.payment_mode',
            'users.user_card_id',
            'users.status as user_status',
            'users.email_notification_status',
            'users.push_notification_status',
            'users.is_verified',
            'users.user_type',
            'users.device_type',
            'users.device_token',
            'users.registration_steps',
            'users.created_at',
            'users.updated_at',
            \DB::raw("'$is_appstore_updated' as is_appstore_updated")
            );
    
    }
    
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOtherCommonResponse($query) {

        return $query->select(
            'users.id as user_id',
            'users.username as username',
            'users.name',
            'users.name as user_name',
            'users.email as email',
            'users.picture as picture',
            'users.mobile as mobile',
            \DB::raw('IFNULL(users.description,"") as description'),
            'users.status as user_status',
            'users.is_verified',
            'users.user_type',
            DB::raw("DATE_FORMAT(users.created_at, '%M %Y') as joined") ,
            'users.created_at',
            'users.updated_at'
            );

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerifiedUser($query) {

        return $query->where('users.is_verified', USER_EMAIL_VERIFIED)->where('users.status', USER_APPROVED);

    }

    /**
     * Get the cards record associated with the user.
     */
    public function userCards() {
        
        return $this->hasMany(UserCard::class, 'user_id');
    }

    /**
     * Get the chats record associated with the user.
     */
    public function userChatMessages() {
        
        return $this->hasMany(ChatMessage::class, 'user_id');
    }

    /**
     * Get the bookings record associated with the user.
     */
    public function userBookings() {
        
        return $this->hasMany(Booking::class, 'user_id');
    }    

    /**
     * Get the bookings record associated with the user.
     */
    public function userBookingPayments() {
        
        return $this->hasMany(BookingPayment::class, 'user_id');
    }

    public function userBillingInfo() {
        
        return $this->hasMany(UserBillingInfo::class, 'user_id');
    }

    /**
     * Get the user fav record associated with the user.
     */

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['first_name'] = $model->attributes['last_name'] = $model->attributes['name'];

            $model->attributes['is_verified'] = USER_EMAIL_VERIFIED;

            if (Setting::get('is_account_email_verification') == YES && envfile('MAIL_USERNAME') && envfile('MAIL_PASSWORD')) { 

                if($model->attributes['login_by'] == 'manual') {

                    $model->generateEmailCode();

                }

            }

            $model->attributes['status'] = USER_APPROVED;

            $model->attributes['payment_mode'] = COD;

            $model->attributes['username'] = routefreestring($model->attributes['name']);

            $model->attributes['unique_id'] = uniqid();

            $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();

            if(in_array($model->attributes['login_by'], ['facebook' , 'google'])) {
                
                $model->attributes['password'] = \Hash::make($model->attributes['social_unique_id']);
            }

        });

        static::created(function($model) {

            $model->attributes['email_notification_status'] = $model->attributes['push_notification_status'] = YES;

            $model->attributes['unique_id'] = "UID"."-".$model->attributes['id']."-".uniqid();

            $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();

            $model->save();

            /**
             * @todo Update total number of users 
             */
        
        });

        static::updating(function($model) {

            $model->attributes['username'] = routefreestring($model->attributes['name']);

            $model->attributes['first_name'] = $model->attributes['last_name'] = $model->attributes['name'];

        });

        static::deleting(function ($model){

            Helper::delete_file($model->picture , PROFILE_PATH_USER);

            $model->userChatMessages()->delete();

            $model->userCards()->delete();

            foreach ($model->userBookings as $key => $booking_details) {
                
                $booking_details->delete();
            }

            $model->wishlists()->delete();

            $model->userBillingInfo()->delete();

        });
    }

    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateEmailCode() {

        $this->attributes['verification_code'] = Helper::generate_email_code();

        $this->attributes['verification_code_expiry'] = Helper::generate_email_expiry();

        // Check Email verification controls and email configurations

        if(Setting::get('is_account_email_verification') == YES && Setting::get('is_email_notification') == YES && Setting::get('is_email_configured') == YES) {

            $this->attributes['is_verified'] = 0;

        } else { 

            $this->attributes['is_verified'] = 1;

        }

        return true;
    
    }

}
