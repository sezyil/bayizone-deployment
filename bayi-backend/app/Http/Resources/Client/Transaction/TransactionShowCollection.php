<?php

namespace App\Http\Resources\Client\Transaction;

use App\Enums\CustomerTransactionsFicheTypeEnum;
use App\Enums\CustomerTransactionsPaymentTypeEnum;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionShowCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (!$this->resource instanceof CustomerTransaction) {
            return [];
        }
        /** @var CustomerTransaction $collection */
        $collection = $this->resource;
        /** @var Currency $currency */
        $currency = $collection->getCurrency;
        $currFormat = fn ($value) => $currency->format($value, true);
        $order = $collection->order ?? null;

        $data = [
            'detail' => [
                'id' => $collection->id,
                'fiche_no' => $collection->fiche_no,
                'fiche_type' => $collection->fiche_type,
                'fiche_type_label' => CustomerTransactionsFicheTypeEnum::description($collection->fiche_type),
                'date' => Carbon::parse($collection->date)->format('d.m.Y'),
                'description' => $collection->description,
                'is_paid' => $collection->is_paid,
                'io_type' => $collection->io_type,
                'amount' => $collection->amount,
                'formatted_amount' => $currFormat($collection->amount),
                'due_date' => $collection->due_date ? Carbon::parse($collection->due_date)->format('d.m.Y') : null,
                'payment_closed_date' => $collection->payment_closed_date ? Carbon::parse($collection->payment_closed_date)->format('d.m.Y') : null,
                'created_at' => $collection->created_at->format('d.m.Y H:i'),
                'updated_at' => $collection->updated_at->format('d.m.Y H:i'),
            ],
            'customer' => [
                'id' => $collection->company_customer_id,
                'name' => $collection->companyCustomer?->company_name,
            ],
            'order' => $order ? [
                'id' => $order->id,
                'order_no' => $order->order_no,
            ] : null,
        ];


        return $data;
    }
}
