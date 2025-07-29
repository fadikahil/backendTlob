<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'type',
        'firebase_id',
        'profile',
        'address',
        'notification',
        'country_code',
        'show_personal_details',
        'is_verified',
        'auto_approve_item',
        'deleted_at',
        'business_name',
        'categories',
        'phone',
        'gender',
        'bio',
        'country',
        'city',
        'state',
        'platform_type',
        'provider_type',
        'status',
        'facebook',
        'twitter',
        'instagram',
        'tiktok',
        'latitude',
        'longitude'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getProfileAttribute($image)
    {
        if (!empty($image) && !filter_var($image, FILTER_VALIDATE_URL)) {
            return url(Storage::url($image));
        }
        return $image;
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function sellerReview()
    {
        return $this->hasMany(SellerRating::class, 'seller_id');
    }

    public function userReviews()
    {
        return $this->hasMany(UserReview::class, 'user_id');
    }

    public function reviewsWritten()
    {
        return $this->hasMany(UserReview::class, 'reviewer_id');
    }

    public function serviceReviews()
    {
        return $this->hasMany(ServiceReview::class, 'user_id');
    }

    public function serviceReviewsWritten()
    {
        return $this->hasMany(ServiceReview::class, 'reviewer_id');
    }

    public function scopeSearch($query, $search)
    {
        $search = "%" . $search . "%";
        return $query->where(function ($q) use ($search) {
            $q->orWhere('email', 'LIKE', $search)
                ->orWhere('mobile', 'LIKE', $search)
                ->orWhere('name', 'LIKE', $search)
                ->orWhere('type', 'LIKE', $search)
                ->orWhere('notification', 'LIKE', $search)
                ->orWhere('firebase_id', 'LIKE', $search)
                ->orWhere('address', 'LIKE', $search)
                ->orWhere('created_at', 'LIKE', $search)
                ->orWhere('updated_at', 'LIKE', $search);
        });
    }

    public function user_reports()
    {
        return $this->hasMany(UserReports::class);
    }

    public function fcm_tokens()
    {
        return $this->hasMany(UserFcmToken::class);
    }

    public function user_purchased_packages()
    {
        return $this->hasMany(UserPurchasedPackage::class);
    }

    public function featured_users() {
        return $this->hasMany(FeaturedUsers::class);
    }

    public function getStatusAttribute($value)
    {
        if ($this->deleted_at) {
            return 0;
        }
        if ($this->expiry_date && $this->expiry_date < Carbon::now()) {
            return 0;
        }
        
        return (isset($value) && $value) ? 1 : 0;
    }

    public function getAutoApproveItemAttribute($value)
    {
        if ($this->is_verified == 1) {
            return 1;
        }
        return $value;
    }

    public function setIsVerifiedAttribute($value)
    {
        $this->attributes['is_verified'] = $value;
        if ($value == 1) {
            $this->attributes['auto_approve_item'] = 1;
        }
    }
}
