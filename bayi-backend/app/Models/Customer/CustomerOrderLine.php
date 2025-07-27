<?php

namespace App\Models\Customer;

use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\VariantTypesEnum;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductVariant;
use App\Models\Catalog\Product\ProductVariantValue;
use App\Models\CustomerShipmentLine;
use App\Models\System\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderLine extends Model
{
    use HasFactory;
    const PREFIX = 'I';

    protected $fillable = [
        'customer_order_id',
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
        'completed',
        'sent_quantity',
        'remaining_quantity',
        'delivered_quantity',
        'status',
        'item_id',
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

        $lastOrder = CustomerOrderLine::whereCustomerOrderId($this->customer_order_id)->orderBy('id', 'desc')->first();
        if ($lastOrder) {
            $lastOrderNo = $lastOrder->item_id;
            $lastOrderNo = explode('-', $lastOrderNo);
            $lastOrderNo = isset($lastOrderNo[1]) ? $lastOrderNo[1] : 1;
            $lastOrderNo = (int)$lastOrderNo + 1;
            $lastOrderNo = str_pad($lastOrderNo, 3, '0', STR_PAD_LEFT);
            while (CustomerOrderLine::whereCustomerOrderId(self::PREFIX . '-' . $lastOrderNo)->exists()) {
                $lastOrderNo = (int)$lastOrderNo + 1;
                $lastOrderNo = str_pad($lastOrderNo, 3, '0', STR_PAD_LEFT);
            }
            return self::PREFIX . '-' . $lastOrderNo;
        } else {
            return self::PREFIX . '-001';
        }
    }

    //customer order
    public function customer_order()
    {
        return $this->belongsTo(CustomerOrder::class);
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
        return $this->hasMany(CustomerOrderLineVariant::class, 'customer_order_line_id');
    }

    //histories
    public function histories()
    {
        return $this->hasMany(CustomerOrderLineHistory::class, 'customer_order_line_id');
    }

    //shipment Line
    public function shipmentLines()
    {
        return $this->hasMany(CustomerShipmentLine::class, 'customer_order_line_id');
    }

    //status label
    public function getStatusLabel()
    {
        return $this->status ? CustomerOrderLineStatusEnum::description($this->status) : null;
    }


    public function saveLines($lines, $customer_order_id, $updateMode = false)
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
            CustomerOrderLine::where('customer_order_id', $customer_order_id)->delete();
        }

        foreach ($lines as $line) {
            $quantityPrice = $line['quantity'] * $line['unit_price'];
            $totalDiscountPrice = $calculateDiscount($quantityPrice, $line['unit_discount_rate']);
            $calculatedDiscountPrice = $quantityPrice - $totalDiscountPrice;
            $calculatedTax = $calculateTax($calculatedDiscountPrice, $line['tax_rate']);
            $lineTotal = $calculatedDiscountPrice + $calculatedTax;
            $product = Product::find($line['product_id']);
            $unit = Unit::find($product->unit_id);

            $lineModel = new CustomerOrderLine();
            $lineModel->customer_order_id = $customer_order_id;
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
            $lineModel->unit_volume = $line['unit_volume'] ?? 0;
            $lineModel->unit_package = $line['unit_package'] ?? 0;
            $lineModel->total_volume = $lineModel->unit_volume * $lineModel->quantity;
            $lineModel->total_package = $lineModel->unit_package * $lineModel->quantity;
            $lineModel->total_discount_price = $totalDiscountPrice;
            $lineModel->total_price = $quantityPrice;
            $lineModel->grand_total = $lineTotal;
            $lineModel->note = $line['note'] ?? '';
            $lineModel->save();

            $lineModel->calculateRemainingQuantity();

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

            $total_price += $quantityPrice;
            $total_tax += $calculatedTax;
            $total_discount += $totalDiscountPrice;
            $grand_total += $lineTotal;
            $total_volume += $lineModel->total_volume;
            $total_package += $lineModel->total_package;
        }

        $customerOffer = CustomerOrder::find($customer_order_id);
        $customerOffer->total_price = $total_price;
        $customerOffer->total_tax = $total_tax;
        $customerOffer->total_discount = $total_discount;
        $customerOffer->grand_total = $grand_total;
        $customerOffer->total_volume = $total_volume;
        $customerOffer->total_package = $total_package;
        $customerOffer->save();
    }


    //calculate remaining quantity and sent quantity for save customer order function
    public function calculateRemainingQuantity()
    {
        $quantity = $this->quantity;
        $sentQuantity = 0;
        $histories = $this->histories;
        foreach ($histories as $history) {
            $sentQuantity += $history->sent_quantity;
        }
        $remainingQuantity = $quantity - $sentQuantity;
        $this->sent_quantity = $sentQuantity;
        $this->remaining_quantity = $remainingQuantity;
        $this->save();
    }

    //rearrange status for shipment module
    public function reArrangeStatusForShipmentModule()
    {
        $totalQuantity = $this->quantity;
        $remainingQuantity = $this->remaining_quantity; //işleme alınmamış miktar
        $sentQuantity = $this->sent_quantity; //sevk edilmiş miktar
        $deliveredQuantity = $this->delivered_quantity; //teslim edilmiş miktar
        $oldStatus = $this->status;

        if ($deliveredQuantity == $totalQuantity) {
            $this->status = CustomerOrderLineStatusEnum::DELIVERED->value;
        } elseif ($sentQuantity > 0 && $sentQuantity < $totalQuantity) {
            $this->status = CustomerOrderLineStatusEnum::PARTIALLY_SHIPPED->value;
        } elseif ($sentQuantity == $totalQuantity) {
            $this->status = CustomerOrderLineStatusEnum::SHIPPED->value;
        }

        if ($oldStatus != $this->status) {
            $this->save();
            $this->histories()->create([
                'status' => $this->status,
                'note' => 'Sevkiyat Modülü Tarafından Güncellendi. : ' . CustomerOrderLineStatusEnum::description($this->status),
            ]);
            if ($this->status == CustomerOrderLineStatusEnum::DELIVERED->value) {
                $this->status = CustomerOrderLineStatusEnum::COMPLETED->value;
                $this->save();
                $this->histories()->create([
                    'status' => $this->status,
                    'note' => 'Sevkiyat Modülü Tarafından Güncellendi. : ' . CustomerOrderLineStatusEnum::description($this->status),
                ]);
            }
        }

        //if delivered set status to completed

    }
}
