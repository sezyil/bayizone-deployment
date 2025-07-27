<?php

namespace App\Models\Catalog\Category;

use App\Models\Catalog\Product\ProductCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_id',
        'parent_id',
    ];

    //boot add uuid
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uniq_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function descriptions()
    {
        return $this->hasOne(CategoryDescription::class, 'category_id');
    }

    public function description()
    {
        return $this->hasOne(CategoryDescription::class, 'category_id')->where('language', app()->getLocale());
    }

    //multilanguage
    public function descriptionsLang()
    {
        return $this->hasMany(CategoryDescription::class, 'category_id');
    }

    //get name
    public function getName()
    {
        return $this->descriptions ? $this->descriptions->name : $this->descriptionsLang()->first()->name;
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function childrens()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }


    public static function Multilevel($items, $pfxName = '', $parent_id = 0)
    {
        $data = [];
        /** @var Categories[] $items */
        foreach ($items as $key => $value) {
            $description = $value->descriptionsLang->where('language', app()->getLocale())->first();
            if (!$description) {
                $description = $value->descriptionsLang->first();
            }
            $newName = ($pfxName != '' ? $pfxName . ' -> ' : '') . $description->name;
            $data[] = [
                'id' => $value->id,
                'parent_id' => $parent_id,
                'name' => $newName,
                'is_default' => (bool)$value->is_default,
                'product_count' => $value->products()->count(),
            ];

            $data = array_merge($data, self::Multilevel(Categories::where([
                ['parent_id', '=', $value->id]
            ])->get(), $newName, $value->id));
        }

        return $data;
    }

    public static function MultilevelForStore($items, $pfxName = '', $parent_id = 0, $customer_id = 0)
    {
        $data = [];
        /** @var Categories[] $items */
        foreach ($items as $key => $value) {
            $description = $value->descriptionsLang->where('language', app()->getLocale())->first();
            if (!$description) {
                $description = $value->descriptionsLang->first();
            }
            $newName = ($pfxName != '' ? $pfxName . ' -> ' : '') . $description->name;
            $data[] = [
                'id' => $value->id,
                'parent_id' => $parent_id,
                'name' => $newName,
                'is_default' => (bool)$value->is_default,
                'product_count' => $value->storeVisibleProducts($customer_id)->count(),
            ];

            $data = array_merge($data, self::MultilevelForStore(
                Categories::where([
                    ['parent_id', '=', $value->id]
                ])->get(),
                $newName,
                $value->id,
                $customer_id
            ));
        }

        return $data;
    }



    public function products()
    {
        return $this->hasMany(ProductCategories::class, 'category_id');
    }

    //store visible products
    public function storeVisibleProducts($customer_id)
    {
        return $this->hasMany(ProductCategories::class, 'category_id')->whereHas('product', function ($query) use ($customer_id) {
            return $query->where('store_visibility', true)->where('customer_id', $customer_id);
        });
    }
}
