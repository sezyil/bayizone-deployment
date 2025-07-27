<?php

namespace App\Http\Controllers\Dealer;

use App\Enums\CustomerOrderStatusEnum;
use App\Exports\CustomerOrderExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Customer\CustomerOrderShowCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomerUser;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderHistory;
use App\Models\Customer\CustomerOrderLine;
use App\Models\System\Cities;
use App\Models\System\Countries;
use App\Models\System\Currency;
use App\Models\System\States;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        /** @var CompanyCustomerUser $user */
        $user = SanctumDealerHelper::getUser();
        $customer_id = $user->customer_id;
        $company_id = $user->company_customer_id;
        //get data from query company_customer_id,customer_email,currency
        $currency = request()->get('currency');
        $firstDate = request()->get('firstDate');
        $secondDate = request()->get('secondDate');
        $dateFilterType = request()->get('dateFilterType');
        $dateFilterActive = $firstDate && $secondDate && $dateFilterType;
        $order_no = request()->get('order_no');
        //check first date and second date is valid date carbon
        if ($dateFilterActive) {
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

        $data = CustomerOrder::whereCustomerId($customer_id)->whereCompanyCustomerId($company_id)
            ->where('status', '!=', CustomerOrderStatusEnum::DRAFT)
            ->when($order_no, function ($query, $order_no) {
                return $query->where('order_no', $order_no);
            })
            ->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })
            ->when($dateFilterActive, function ($query, $dateFilterType) use ($firstDate, $secondDate) {
                $availableDateFilters = ['order_date', 'create_date'];
                if (!in_array($dateFilterType, $availableDateFilters)) {
                    return $query;
                } else {
                    return $query->whereBetween($dateFilterType, [$firstDate, $secondDate]);
                }
            })
            ->get()
            ->transform(function ($item) {
                /** @var CustomerOrder $item */
                return [
                    'id' => $item->id,
                    'company_customer_id' => $item->company_customer_id,
                    'order_no' => $item->order_no,
                    'order_date' => Carbon::parse($item->order_date)->format('d.m.Y'),
                    'total_price' => $item->getCurrency?->format($item->total_price, true) ?? $item->total_price,
                    'currency' => $item->currency,
                    'currency_name' => $item->getCurrency->title,
                    'status' => $item->status,
                    'uri' => $item->generatePreviewUri(),
                    'status_text' => CustomerOrderStatusEnum::description($item->status),
                    'created_at' => $item->created_at->format('d.m.Y H:i:s'),
                    'updated_at' => $item->updated_at->format('d.m.Y H:i:s'),
                ];
            });
        return DatatableResponder::sendResponse($data, $data->count());
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        /** @var CompanyCustomerUser $user */
        $user = SanctumDealerHelper::getUser();
        $customer_id = $user->customer_id;
        $company_id = $user->company_customer_id;
        $order = CustomerOrder::whereCustomerId($customer_id)->whereCompanyCustomerId($company_id)->whereId($id)->firstOrFail();

        $data = new CustomerOrderShowCollection($order);
        return Responder::send_success(data: $data);
    }

    //export to excel
    public function exportToExcel(string $id): JsonResponse|BinaryFileResponse
    {

        $lang = request()->get('lang', 'tr');
        if (!in_array($lang, ['tr', 'en'])) {
            $lang = 'tr';
        }

        $customer_id = SanctumDealerHelper::getUser()->customer_id;
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $id)->first();
        if ($customerOrder == null) {
            return Responder::send_response(false, 'Customer order not found.',  404);
        }

        $exportClass = new CustomerOrderExport($customerOrder, $lang);
        /* return Excel::download($exportClass, 'offer.xlsx'); */
        //api response

        $fileName = $customerOrder->order_no . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download($exportClass, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
