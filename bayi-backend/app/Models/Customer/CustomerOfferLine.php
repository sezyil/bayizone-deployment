<?php

namespace App\Models\Customer;

use App\Enums\VariantTypesEnum;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariantValue;
use App\Models\System\Unit;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOfferLine extends Model
{
    use HasFactory, HasUuids;
    const PREFIX = 'I';

    protected $fillable = [
        'customer_offer_id',
        'product_id',
        'product_name',
        'product_code',
        'product_unit',
        'product_image_url',
        'unit_id',
        'quantity',
        'unit_price',
        'tax_rate',
        'unit_discount_price',
        'unit_discount_rate',
        'total_discount_price',
        'total_price',
        'grand_total',
        'note',
        'unit_volume',
        'unit_package',
        'total_volume',
        'total_package',
    ];

    //boot
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->item_id = $model->generateItemId();
        });
    }

    //generate item id
    public function generateItemId()
    {

        $lastOrder = CustomerOfferLine::whereCustomerOfferId($this->customer_offer_id)->orderBy('id', 'desc')->first();
        if ($lastOrder) {
            $lastOrderNo = $lastOrder->item_id;
            $lastOrderNo = explode('-', $lastOrderNo);
            $lastOrderNo = isset($lastOrderNo[1]) ? $lastOrderNo[1] : 1;
            $lastOrderNo = (int)$lastOrderNo + 1;
            $lastOrderNo = str_pad($lastOrderNo, 3, '0', STR_PAD_LEFT);
            while (CustomerOfferLine::whereCustomerOfferId(self::PREFIX . '-' . $lastOrderNo)->exists()) {
                $lastOrderNo = (int)$lastOrderNo + 1;
                $lastOrderNo = str_pad($lastOrderNo, 3, '0', STR_PAD_LEFT);
            }
            return self::PREFIX . '-' . $lastOrderNo;
        } else {
            return self::PREFIX . '-001';
        }
    }

    //customer offer
    public function customer_offer()
    {
        return $this->belongsTo(CustomerOffer::class);
    }

    //unit
    public function unitType()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    //product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //variants
    public function variants()
    {
        return $this->hasMany(CustomerOfferLineVariant::class, 'customer_offer_line_id');
    }


    public function saveLines($lines, $customer_offer_id, $updateMode = false)
    {
        $total_price = 0; // toplam fiyat (kdv ve indirim hariç)
        $total_tax = 0; // toplam kdv
        $total_discount = 0; // toplam indirim
        $grand_total = 0; // toplam fiyat (kdv ve indirim dahil)
        $total_volume = 0;
        $total_package = 0;

        $calculateTax = function ($price, $taxRate) {
            return $price * $taxRate / 100;
        };

        $calculateDiscount = function ($price, $discountRate) {
            return $price * $discountRate / 100;
        };

        if ($updateMode) {
            CustomerOfferLine::where('customer_offer_id', $customer_offer_id)->delete();
        }

        foreach ($lines as $line) {
            $quantityPrice = $line['quantity'] * $line['unit_price'];
            $totalDiscountPrice = $calculateDiscount($quantityPrice, $line['unit_discount_rate']);
            $calculatedDiscountPrice = $quantityPrice - $totalDiscountPrice;
            $calculatedTax = $calculateTax($calculatedDiscountPrice, $line['tax_rate']);
            $lineTotal = $calculatedDiscountPrice + $calculatedTax;
            $product = Product::find($line['product_id']);
            $unit = Unit::find($product->unit_id);

            $lineModel = new CustomerOfferLine();
            $lineModel->customer_offer_id = $customer_offer_id;
            $lineModel->product_id = $line['product_id'];
            $lineModel->product_name = $product->description->name;
            $lineModel->product_code = $product->model;
            $lineModel->product_unit = $unit->description?->name;
            $lineModel->product_image_url = $product->image;
            $lineModel->unit_id = $unit->id;
            $lineModel->quantity = $line['quantity'];
            $lineModel->unit_price = $line['unit_price'];
            $lineModel->tax_rate = $line['tax_rate'];
            $lineModel->unit_discount_price = $calculateDiscount($line['unit_price'], $line['unit_discount_rate']);
            $lineModel->unit_discount_rate = $line['unit_discount_rate'];
            $lineModel->total_discount_price = $totalDiscountPrice;
            $lineModel->total_price = $quantityPrice;
            $lineModel->grand_total = $lineTotal;
            $lineModel->unit_volume = $line['unit_volume'] ?? 0;
            $lineModel->unit_package = $line['unit_package'] ?? 0;
            $lineModel->total_volume = $lineModel->unit_volume * $lineModel->quantity;
            $lineModel->total_package = $lineModel->unit_package * $lineModel->quantity;
            $lineModel->note = $line['note'] ?? '';
            $lineModel->save();



            $total_volume += $lineModel->total_volume;
            $total_package += $lineModel->total_package;

            $total_price += $quantityPrice;
            $total_tax += $calculatedTax;
            $total_discount += $totalDiscountPrice;
            $grand_total += $lineTotal;

            $color = isset($line['color']) && is_array($line['color']) ? $line['color'] : [];
            if ($color) {
                foreach ($color as $colorItem) {
                    $productVariantValue = ProductVariantValue::where('product_variant_id', $colorItem['product_variant_id'])
                        ->where('variant_value_id', $colorItem['value_id'])
                        ->first();
                    if ($productVariantValue) {
                        $lineModel->variants()->create([
                            'type' => VariantTypesEnum::COLOR->value,
                            'product_variant_id' => $colorItem['product_variant_id'],
                            'product_variant_value_id' => $productVariantValue->id,
                        ]);
                    }
                }
            }

            $dimension = isset($line['dimension']) && is_array($line['dimension']) ? $line['dimension'] : [];
            if ($dimension) {
                foreach ($dimension as $dimensionItem) {
                    $productVariantValue = ProductVariantValue::where('product_variant_id', $dimensionItem['product_variant_id'])
                        ->where('id', $dimensionItem['value_id'])
                        ->first();
                    if ($productVariantValue) {
                        $lineModel->variants()->create([
                            'type' => VariantTypesEnum::DIMENSION->value,
                            'product_variant_id' => $dimensionItem['product_variant_id'],
                            'product_variant_value_id' => $productVariantValue->id,
                        ]);
                    }
                }
            }
        }

        $customerOffer = CustomerOffer::find($customer_offer_id);
        $customerOffer->total_price = $total_price;
        $customerOffer->total_tax = $total_tax;
        $customerOffer->total_discount = $total_discount;
        $customerOffer->grand_total = $grand_total;
        $customerOffer->total_volume = $total_volume;
        $customerOffer->total_package = $total_package;
        $customerOffer->save();
    }
}
