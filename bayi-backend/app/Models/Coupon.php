<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    const PRODUCT_GROUP_ALL = 'all';
    const PRODUCT_GROUP_SUBSCRIPTION = 'subscription';
    const PRODUCT_GROUP_ADDON = 'addon';

    protected $fillable = [
        'code',
        'percentage',
        'product_group',
        'customer_based',
        'limit',
        'expires_at',
        'is_active',
        'is_redeemed',
        'redeemed_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'use_count',
        'currency',
        'only_cash'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'is_active' => 'boolean',
        'is_redeemed' => 'boolean',
        'customer_based' => 'boolean',
        'only_cash' => 'boolean'
    ];

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(Admin::class, 'deleted_by');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'coupon_customer', 'coupon_id', 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }

    //calculate discount
    public function calculateDiscount($total)
    {
        return $total * $this->percentage / 100;
    }


    #region Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    //has limit
    public function scopeHasLimit($query)
    {
        return $query->whereNotNull('limit');
    }

    //limit reached
    public function scopeLimitReached($query)
    {
        return $query->whereColumn('use_count', '>=', 'limit');
    }
    #endregion Scopes

    public function isLimitReached()
    {
        return $this->limit && $this->use_count >= $this->limit;
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isOnlyCash()
    {
        return $this->only_cash;
    }

    public function isCustomerBased()
    {
        return $this->customer_based;
    }

    public function calculateUseCount()
    {
        $this->use_count = $this->orders()->notPaid()->count();
    }

    //sync orders
    public function syncUseCount()
    {
        $this->calculateUseCount();
        if ($this->isLimitReached()) {
            $this->is_active = false;
        }
        $this->save();
    }

    //sync for update
    public function syncForUpdateDetail()
    {
        $orders = $this->orders()->notPaid()->active()->get();
        $customers = $this->customers;
        $orders->each(function ($order) {
            /** @var Order $order */
            $order->calculatePrices();
        });
    }
}
