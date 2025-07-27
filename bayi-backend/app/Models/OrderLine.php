<?php

namespace App\Models;

use App\Enums\SubscriptionTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_SUBSCRIPTION_ADDON = 'subscription_addon';
    use HasFactory;

    protected $fillable = [
        'order_id',
        'type',
        'name',
        'price',
        'item_id',
        'item_data',
        'tax_rate',
        'tax_amount',
        'quantity',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'item_data' => 'array',
    ];

    //hidden fields
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    #region Scopes
    //isYearlyAndSubscription
    public function scopeIsYearlyAndSubscription($query)
    {
        return $query->where('type', self::TYPE_SUBSCRIPTION)
            ->whereJsonContains('item_data', ['duration' => 12]);
    }
    #endregion

    //activateItem
    public function activateItem()
    {
        if ($this->type === self::TYPE_SUBSCRIPTION) {
            $this->registerSubscriptionItem();
        } elseif ($this->type === self::TYPE_SUBSCRIPTION_ADDON) {
            $this->registerSubscriptionAddonItem();
        }
    }

    private function registerSubscriptionItem()
    {
        $planId = $this->item_id;
        $plan = Plan::active()->findOrFail($planId);
        //$itemData is ['duration' => $price->month,] //ends at 23:59:59
        $ends_at = now()->addMonths($this->item_data['duration'])->endOfDay();
        $subscription = Subscription::create([
            'customer_id' => $this->order->customer_id,
            'plan_id' => $this->item_id,
            'order_id' => $this->order_id,
            'is_trial' => false,
            'trial_ends_at' => null,
            'starts_at' => now(),
            'ends_at' => $ends_at,
            'canceled_at' => null,
            'is_active' => true,
            'user_count' => $plan->user_count,
            'left_user_count' => $plan->user_count,
            'sales_management' => $plan->sales_management,
            'simple_accounting' => $plan->simple_accounting,
            'online_store' => $plan->online_store,
            'product_count' => $plan->product_count,
            'left_product_count' => $plan->product_count,
            'provider_panel_count' => $plan->provider_panel_count,
            'left_provider_panel_count' => $plan->provider_panel_count,
        ]);

        $this->update([
            'item_data' => [
                ...$this->item_data,
                ...$subscription->toArray()
            ],
        ]);

        //disable trial subscriptions
        Subscription::where('customer_id', $this->order->customer_id)
            ->where('is_trial', true)
            ->update(['is_active' => false]);





        //disable non-paid and active subscriptions
        $old_orders = $this->order->customer->orders()
            ->where('id', '!=', $this->order_id)
            ->where('is_paid', false)
            ->where('is_active', true)
            //where order_line -> type=subscription
            ->whereHas('lines', function ($query) {
                $query->where('type', self::TYPE_SUBSCRIPTION);
            })
            ->get();
        foreach ($old_orders as $old_order) {
            $old_order->is_active = false;
            $old_order->save();
        }
    }

    private function registerSubscriptionAddonItem()
    {
        $findActiveSubscription = $this->order->customer->activeSubscription;
        if ($findActiveSubscription) {
            $itemData = $this->item_data;
            $addonType = isset($itemData['type']) ? $itemData['type'] : null;
            $isBoolean = isset($itemData['is_boolean']) ? (bool)$itemData['is_boolean'] : false;
            $amount = isset($itemData['amount']) ? (int)$itemData['amount'] : 1;
            if ($addonType) {
                if ($isBoolean) {
                    $findActiveSubscription->{$addonType} = true;
                } else {
                    $is_bulk = isset($itemData['is_bulk']) ? (bool)$itemData['is_bulk'] : false;
                    $bulk_quantity = isset($itemData['bulk_quantity']) ? (int)$itemData['bulk_quantity'] : 1;
                    $quantity = 0;
                    if ($is_bulk) {
                        $quantity = $bulk_quantity * $amount;
                    } else {
                        $quantity = $amount;
                    }
                    if ($addonType === SubscriptionTypesEnum::PROVIDER_PANEL_COUNT->value) {
                        $findActiveSubscription->provider_panel_count += $quantity;
                        $findActiveSubscription->left_provider_panel_count += $quantity;
                    } elseif ($addonType === SubscriptionTypesEnum::PRODUCT_COUNT->value) {
                        $findActiveSubscription->product_count += $quantity;
                        $findActiveSubscription->left_product_count += $quantity;
                    } elseif ($addonType === SubscriptionTypesEnum::USER_COUNT->value) {
                        $findActiveSubscription->user_count += $quantity;
                        $findActiveSubscription->left_user_count += $quantity;
                    }
                }
                $findActiveSubscription->save();
            }
        }
    }
}
