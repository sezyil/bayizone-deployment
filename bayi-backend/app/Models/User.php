<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\System\Role;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, HasRoles, CanResetPassword;
    protected $table = "user";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'password',
        'customer_id',
        'phone',
        'role_id',
        'password_need_change'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'customer_id',
        'password_need_change'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'is_super_user' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    //get fullname
    public function getFullname()
    {
        return $this->fullname;
    }

    //get authentication related data
    public function getAuthData($withToken = false)
    {
        setPermissionsTeamId($this->customer_id);
        $subscription = $this->customer->activeSubscription?->makeHidden([
            'id',
            'customer_id',
            'plan_id',
            'order_id',
            'starts_at',
            'canceled_at',
            'created_at',
            'updated_at'
        ]) ?? null;
        if ($subscription) {
            //add plan name to subscription
            //unset plan
            if (isset($subscription->plan) && $subscription->plan) {
                $subscription->plan_name = $subscription->plan->name;
                $ends_at = $subscription->ends_at->format('Y-m-d H:i:s');
                unset($subscription->plan);
                $subscription = $subscription->toArray();
                $subscription['ends_at'] = $ends_at;
            }
        }
        $data = [
            'user' => [
                'id' => $this->id,
                'fullname' => $this->fullname,
                'email' => $this->email,
                'role' => Role::where('id', $this->role_id)->first()->nameToView(),
                'customer_id' => $this->customer_id,
                'language' => $this->language,
                'image' => $this->customer->image,
                'hasCompanyInfo' => $this->customer->address ? true : false,
                'is_super_user' => $this->is_super_user,
                'wizard_completed' => $this->customer->wizard_completed,
                'ai_support' => $this->customer->ai_support,
            ],
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'subscription' => $subscription
        ];
        if ($withToken) {
            $data['token'] = $this->createToken('MyApp', ['user'], now()->addHours(3))->plainTextToken;
        }
        return $data;
    }
}
