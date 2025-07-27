<?php

namespace App\Models;

use App\Models\Catalog\Attribute\AttributeGroup;
use App\Models\Catalog\Attribute\Attributes;
use App\Models\Catalog\Category\Categories;
use App\Models\Catalog\Product\Product;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Cities;
use App\Models\System\Countries;
use App\Models\System\States;
use App\Services\Client\Product\DummyProductService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Str;

class Customer extends Authenticatable
{
    use HasFactory, HasUuids;
    protected $table = "customer";

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = [
        'authorized_person',
        'plan_id',
        'firm_name',
        'tax_no',
        'tax_administration',
        'address',
        'postcode',
        'country_id',
        'app_key',
        'secret_key',
        'state_id',
        'city_id',
        'email',
        'phone',
        'fax',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'wizard_completed' => 'boolean',
        'ai_support' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(Attributes::class);
    }
    public function attributeGroups()
    {
        return $this->hasMany(AttributeGroup::class);
    }

    public function customerOrders()
    {
        return $this->hasMany(CustomerOrder::class);
    }

    //company customer
    public function companyCustomers()
    {
        return $this->hasMany(CompanyCustomer::class);
    }

    //customer bank account
    public function customerBankAccounts()
    {
        return $this->hasMany(CustomerBankAccount::class);
    }

    //customer transactions
    public function customerTransactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    //country
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    //state
    public function state()
    {
        return $this->belongsTo(States::class);
    }

    //city
    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    //subscription
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->active()->notExpired();
    }

    //orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    //category
    public function categories()
    {
        return $this->hasMany(Categories::class);
    }

    //shipments
    public function shipments()
    {
        return $this->hasMany(CustomerShipment::class, 'customer_id', 'id');
    }

    //variants
    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    //variant values
    public function variantValues()
    {
        return $this->hasMany(VariantValue::class);
    }

    //check wizard completed
    public function checkWizardCompleted()
    {
        if (
            $this->firm_name &&
            $this->tax_no &&
            $this->tax_administration &&
            $this->address &&
            $this->country_id &&
            $this->phone &&
            $this->authorized_person &&
            $this->image
        ) {
            $this->wizard_completed = true;
            $this->save();
        }

        return $this->wizard_completed;
    }

    //activate trial

    public function activateTrial()
    {
        if (!$this->wizard_completed) {
            return false;
        }
        if ($this->activeSubscription) {
            return false;
        }

        $trial = Plan::trial()->first();
        $end_at = now()->addDays($trial->trial_days)->endOfDay();
        $subscription = Subscription::create([
            'customer_id' => $this->id,
            'plan_id' => $trial->id,
            'order_id' => null,
            'is_trial' => true,
            'trial_ends_at' => $end_at,
            'starts_at' => now(),
            'ends_at' => $end_at,
            'canceled_at' => null,
            'is_active' => true,
            'user_count' => $trial->user_count,
            'left_user_count' => $trial->user_count,
            'sales_management' => $trial->sales_management,
            'simple_accounting' => $trial->simple_accounting,
            'online_store' => $trial->online_store,
            'product_count' => $trial->product_count,
            'left_product_count' => $trial->product_count,
            'provider_panel_count' => $trial->provider_panel_count,
            'left_provider_panel_count' => $trial->provider_panel_count,
        ]);

        (new DummyProductService($this->id))->create();

        return true;
    }
}
