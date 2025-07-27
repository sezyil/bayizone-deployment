<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'plan_id',
        'order_id',
        'is_trial',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
        'is_active',
        'user_count',
        'left_user_count',
        'sales_management',
        'simple_accounting',
        'online_store',
        'product_count',
        'left_product_count',
        'provider_panel_count',
        'left_provider_panel_count',
    ];

    protected $casts = [
        'is_trial' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'sales_management' => 'boolean',
        'simple_accounting' => 'boolean',
        'online_store' => 'boolean',
    ];

    #region [Subscription Relationships]
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function addons()
    {
        return $this->hasMany(SubscriptionAddon::class);
    }
    #endregion

    #region [Subscription Status]
    /**
     * Check if the subscription is active
     *
     * @return bool
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the subscription is trial
     *
     * @return bool
     */
    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    /**
     * Check if the subscription is not trial
     *
     * @return bool
     */
    public function scopeNotExpired($query)
    {
        return $query->where('ends_at', '>', now());
    }
    #endregion

    /**
     * Get the left months of the subscription
     *
     * @return int
     */
    public function getLeftMonths()
    {
        $days = now()->diffInDays($this->ends_at);
        $months = $days / 30;
        //if more than or equal 15 days round up months
        if ($days % 30 >= 15) {
            $months = ceil($months);
        } else {
            if ($months < 1) {
                $months = 1;
            }
            $months = floor($months);
        }
        return $months;
    }

    #region [Check Subscription Features]

    public function hasSalesManagement()
    {
        return $this->sales_management;
    }

    public function hasSimpleAccounting()
    {
        return $this->simple_accounting;
    }

    public function hasOnlineStore()
    {
        return $this->online_store;
    }

    public function hasProductCount()
    {
        return $this->left_product_count > 0;
    }

    public function hasUserCount()
    {
        return $this->left_user_count > 0;
    }

    public function hasProviderPanelCount()
    {
        return $this->left_provider_panel_count > 0;
    }

    //is trial
    public function isTrial()
    {
        return $this->is_trial;
    }
    #endregion

    #region [Subscription Helper Functions]
    //increment product count
    public function increaseProductCount($count)
    {
        $this->left_product_count += $count;
        $this->save();
    }

    //decrement product count
    public function decreaseProductCount($count)
    {
        $this->left_product_count -= $count;
        $this->save();
    }

    //increment user count
    public function increaseUserCount($count)
    {
        $this->left_user_count += $count;
        $this->save();
    }

    //decrement user count
    public function decreaseUserCount($count)
    {
        $this->left_user_count -= $count;
        $this->save();
    }

    //increment provider panel count
    public function increaseProviderPanelCount($count)
    {
        $this->left_provider_panel_count += $count;
        $this->save();
    }

    //decrement provider panel count
    public function decreaseProviderPanelCount($count)
    {
        $this->left_provider_panel_count -= $count;
        $this->save();
    }
    #endregion
}
