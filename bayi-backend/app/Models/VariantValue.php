<?php

namespace App\Models;

use App\Models\System\Language;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantValue extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'variant_type',
        'value',
        'is_default',
        'customer_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'variant_type', 'type');
    }

    public function descriptions()
    {
        return $this->hasMany(VariantValueDescription::class);
    }

    public function description($language = null)
    {
        if ($language) {
            $description = $this->descriptions->where('language', $language)->first();
            if ($description) {
                return $description;
            }
        }

        return $this->descriptions->first();
    }

    #region Scopes
    //default
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeNotDefault($query)
    {
        return $query->where('is_default', false);
    }

    //default and customer
    public function scopeDefaultAndCustomer($query, $customerId)
    {
        //is default or customer_id
        return $query->where(function ($query) use ($customerId) {
            $query->where('is_default', true)
                ->orWhere('customer_id', $customerId);
        });
    }
    #endregion

    //get name
    public function getName($language = null)
    {
        $_lang = app()->getLocale();
        if ($language) {
            $_lang = $language;
        }
        $description = $this->description($_lang);
        if ($description) {
            return $description->name;
        } else {
            return $this->descriptions?->first()?->name ?? null;
        }
    }
    //get keys lang based
    public function getNames()
    {
        $names = [];
        $descriptions = $this->descriptions;
        foreach (Language::all() as $language) {
            $description = $descriptions->where('language', $language->code)->first();
            $names[$language->code] = null;
            if ($description) {
                $names[$language->code] = $description->name;
            }
        }

        return $names;
    }
}
