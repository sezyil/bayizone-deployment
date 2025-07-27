<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\CustomerOfferPreviewCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\Customer\CustomerOffer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;


class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        //get data from query company_customer_id,customer_email,currency
        $currency = request()->get('currency') ?? null;
        $dateFilterType = request()->get('dateFilterType') ?? null;
        $firstDate = request()->get('firstDate') ?? null; //'offer_create_date' | 'offer_due_date'
        $secondDate = request()->get('secondDate') ?? null;
        //check first date and second date is valid date carbon
        if ($firstDate && $secondDate && $dateFilterType) {
            try {
                $firstDate = Carbon::parse($firstDate);
                $secondDate = Carbon::parse($secondDate);
            } catch (\Exception $e) {
                return Responder::send_unprocessable('Tarihler geçersiz.');
            }
            if ($firstDate->greaterThan($secondDate)) {
                return Responder::send_unprocessable('İlk tarih ikinci tarihten büyük olamaz.');
            }
        }

        $data = CustomerOffer::where('customer_id', $customer_id)->where('company_customer_id', $company_id)
            ->whereNot('status', 'draft')
            ->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })->when($dateFilterType, function ($query, $dateFilterType) use ($firstDate, $secondDate) {
                if ($dateFilterType == 'offer_create_date') {
                    return $query->whereBetween('offer_date', [$firstDate, $secondDate]);
                } else if ($dateFilterType == 'offer_due_date') {
                    return $query->whereBetween('offer_due_date', [$firstDate, $secondDate]);
                }
            })
            ->get()
            ->transform(function ($item) {
                /** @var CustomerOffer $item */
                return [
                    'id' => $item->id,
                    'company_customer_id' => $item->company_customer_id,
                    'company_customer_name' => $item->company_customer->company_name,
                    'grand_total' => $item->grand_total,
                    'offer_date' => $item->offer_date,
                    'offer_due_date' => $item->offer_due_date,
                    'offer_no' => $item->offer_no,
                    'currency' => $item->currency,
                    'currency_name' => $item->getCurrency->title,
                    'mail_notification_date' => $item->mail_notification_date,
                    'password' => $item->password,
                    'preview_url' => route('proforma-invoice.approval', ['proformaId' => $item->id]),
                    'status' => $item->status,
                ];
            });
        return DatatableResponder::sendResponse($data, $data->count());
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer_id = SanctumHelper::customer_id();
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('id', $id)->with('lines')->get();
        if ($customerOffer->isEmpty()) {
            return Responder::send_response(false, 'Customer offer not found.',  404);
        }
        $data = new CustomerOfferPreviewCollection($customerOffer->first());
        return Responder::send_success("", $data);
    }
}
