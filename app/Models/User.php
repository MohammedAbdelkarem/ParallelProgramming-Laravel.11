<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Constants\MediaCollection;
use App\Constants\Resources;
use App\Enums\GenderEnum;
use App\Models\Administration\Log\BanLog;
use App\Models\Administration\Profile\AdminProfile;
use App\Models\Scopes\User\LoadUserWalletScope;
use App\Models\System\Info\PrivacyPolicy;
use App\Models\System\Notification\Notification;
use App\Models\System\Role\Role;
use App\Models\System\SystemSetting;
use App\Models\Users\Profile\ArchivedUser;
use App\Models\Users\Profile\LoginHistory;
use App\Models\Users\Profile\UserDevice;
use App\Models\Users\Profile\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject , HasMedia
{
    use HasFactory, Notifiable, SoftDeletes , InteractsWithMedia;

    protected $with = ["role"];

    protected $guarded = [
        'id',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    //JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function jwtTokens(): HasMany
    {
        return $this->hasMany(JWTPersonalTokens::class, "user_id");
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaCollection::USER_COLLECTION)->singleFile();
    }

    public function isRegularUser(): bool
    {
        return $this->role_id === 3;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1 || $this->role_id === 2;
    }


    public function isSuperAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isRegularAdmin(): bool
    {
        return $this->role_id === 2;
    }
    //Account Relations

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class, "user_id");
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, "role_id");
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, "user_id");
    }

    public function adminProfile(): HasOne
    {
        return $this->hasOne(AdminProfile::class, "user_id");
    }

    public function adminsCreated(): HasMany
    {
        return $this->hasMany(AdminProfile::class, "created_by");
    }

    public function userDevices(): HasMany
    {
        return $this->hasMany(UserDevice::class, "user_id");
    }

    public function archivedAccount(): HasOne
    {
        return $this->hasOne(ArchivedUser::class, "user_id");
    }

    public function otp(): HasOne
    {
        return $this->hasOne(OTP::class, 'user_id');
    }

    //Notifications
    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class, 'user_notification')
            ->withTimestamps()
            ->withPivot('is_read');
    }

    public function createdNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'created_by');
    }

    
    public function systemSettingsUpdated(): HasMany
    {
        return $this->hasMany(SystemSetting::class, 'update_by');
    }

    //Ban System
    public function bans(): HasMany
    {
        return $this->hasMany(BanLog::class, "banned_id");
    }

    public function bannedByMe(): HasMany
    {
        return $this->hasMany(BanLog::class, "banned_by_id");
    }

    public function unbannedByMe(): HasMany
    {
        return $this->hasMany(BanLog::class, "unbanned_by_id");
    }
    /**
     * Relations
     */

    
    public function currentProfile()
    {
        if($this->isAdmin())
            return $this->adminProfile;
        if ($this->isDriver())
            return $this->driver;
        if ($this->isDriverCompany())
            return $this->driverCompany;
        if ($this->isCustomClearenceCompany())
            return $this->customClearenceCompany;
        if ($this->isCustomer())
            return $this->customer;

        // Default: customer (the user itself)
        return $this;
    }

    public function currentProfileId()
    {
        if($this->isAdmin())
            return $this->adminProfile->id;
        if ($this->isDriver())
            return $this->driver->id;
        if ($this->isDriverCompany())
            return $this->driverCompany->id;
        if ($this->isCustomClearenceCompany())
            return $this->customClearenceCompany->id;
        if ($this->isCustomer())
            return $this->customer->id;
    }

    public function currentProfileType()
    {
        if($this->isAdmin())
            return AdminProfile::class;
        if ($this->isDriver())
            return Driver::class;
        if ($this->isDriverCompany())
            return DriverCompany::class;
        if ($this->isCustomClearenceCompany())
            return CustomClearenceCompany::class;
        if ($this->isCustomer())
            return Customer::class;
    }



    //Scopes

    /**
     * Search users by given search criteria
     * This scope will search 'name' and email columns
     * with LIKE operator and case insensitive
     *
     * @param Builder $query
     * @param string|null $search
     */
    public function scopeSearchName(Builder $query, string|null $search)
    {
        $query->when($search, function (Builder $q) use ($search) {
            $q->where("name", 'like', '%' . strtolower($search) . '%')
                ->orWhere("phone_number", "like", "%$search%");
            if (Auth::user() && Auth::user()->role_id != 3)
                $q->orWhere("email", "like", "%$search%");
        });
    }

    /**
     * Search users by given role(s) and filter by criteria below
     * 1. User account should be completed ('name' is not null)
     * 2. User have an active account (deactive_at is null)
     * 3. User account should be verified (account_verified_at is not null)
     *
     * @param Builder $query
     * @param array $role_id
     *
     */
    public function scopeUsersSearchCriteria(Builder $query, $checkBan = true)
    {
        $query->whereIn("role_id", [5,4])
            ->whereNotNull(['name', 'account_verified_at'])            //User account is completed and active
            ->whereNull("deactive_at")              //User have an active account
            ->when($checkBan, function (Builder $q) {
                $q->whereHas("profile", function ($query) {
                    $query->where("banned_until", "<", Carbon::now())
                        ->orWhereNull("banned_until");
                });
            });
    }

    public function scopeFilter($query, $data)
    {
        return $query

        ->when(isset($data['search']) , function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone_number', 'like', '%' . $search . '%');
        });
    }

    public static function findByIdOrFail($id, $with = [], $withTrashed = false, $selectedColumns = null)
    {
        return findByIdOrFail(
            self::class,
            $id,
            GenderEnum::MALE->value,
            Resources::RES_USER,
            $with,
            $withTrashed,
            $selectedColumns
        );
    }
}
