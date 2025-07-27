<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'is_active',
        'name',
        'attributes',
        'is_featured',
        'is_trial',
        'trial_days',
        'user_count',
        'sales_management',
        'simple_accounting',
        'online_store',
        'product_count',
        'provider_panel_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'attributes' => 'array',
        'is_featured' => 'boolean',
        'is_trial' => 'boolean',
        'trial_days' => 'integer',
        'user_count' => 'integer',
        'sales_management' => 'boolean',
        'simple_accounting' => 'boolean',
        'online_store' => 'boolean',
        'product_count' => 'integer',
        'provider_panel_count' => 'integer',
    ];

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    //active plans
    public function scopeActive($query, bool $isActive = true)
    {
        return $query->where('is_active', $isActive);
    }

    //trial plans
    public function scopeTrial($query, bool $isTrial = true)
    {
        return $query->where('is_trial', $isTrial);
    }
}
