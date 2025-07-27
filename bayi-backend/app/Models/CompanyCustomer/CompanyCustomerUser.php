<?php

namespace App\Models\CompanyCustomer;

use App\Models\Customer;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CompanyCustomerUser extends Authenticatable
{
    use HasFactory, HasApiTokens, HasUuids, CanResetPassword, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'fullname',
        'email',
        'password',
        'company_customer_id',
        'phone',
        'password_need_change',
        'is_main_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'company_customer_id',
        'password_need_change'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companyCustomer()
    {
        return $this->belongsTo(CompanyCustomer::class, 'company_customer_id', 'id');
    }

    //get auth data
    public function getAuthData($withToken = false)
    {
        $data = ['user' => [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'fullname' => $this->fullname,
            'firm_name' => $this->companyCustomer->company_name . ' | ' . $this->customer->firm_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'password_need_change' => $this->password_need_change,
            'image' => $this->customer->image,
            'language' => $this->language,
            'company_code' => $this->companyCustomer->code,
        ]];

        if ($withToken) {
            $data['token'] = $this->createToken('MyApp', ['company_customer_user'], now()->addHours(3))->plainTextToken;
        }

        return $data;
    }
}
