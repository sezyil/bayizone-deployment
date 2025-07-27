<?php

namespace App\View\Components\Proforma;

use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOfferLine;
use App\Models\System\Currency;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemTable extends Component
{
    /** @var CustomerOfferLine[] */
    public $items;

    //currency
    public $currency;

    //international order
    public $international;

    //total discount
    public $total_discount;

    //grand total
    public $grand_total;

    /**
     * Create a new component instance.
     */
    public function __construct(CustomerOffer $data)
    {
        $this->items = $data->lines;
        $this->currency = $data->currency;
        $this->international = $data->international_order;
        //grand total
        $this->grand_total = $data->grand_total;
        //total discount
        $this->total_discount = $data->total_discount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.proforma.item-table');
    }

    public function total(): float|int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total_price;
        }

        return $total;
    }

    //currency format
    public function currencyFormat($number): string
    {
        return Currency::formatCurrency($number, $this->currency, true);
    }

    public function calculateVolume(): float|int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total_volume;
        }

        return $total;
    }

    public function calculatePackage(): float|int
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total_package;
        }

        return $total;
    }

    //calculate quantity
    public function calculateQuantity()
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->quantity;
        }

        return $quantity;
    }
}
