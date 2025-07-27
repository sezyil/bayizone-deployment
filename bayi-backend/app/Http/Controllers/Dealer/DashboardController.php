<?php

namespace App\Http\Controllers\Dealer;

use App\Enums\CustomerTransactionsFicheTypeEnum;
use App\Enums\OfferRequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\Responder;
use App\Models\Catalog\Product\Product;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOffer;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\OfferRequest;
use App\Models\System\Currency;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $customer_id = SanctumDealerHelper::customer_id();
        $company_id = SanctumDealerHelper::company_customer_id();
        $data = [
            'offers' => [
                'total' => 0,
            ],
            'offer_request' => [
                'pending' => 0,
                'accepted' => 0,
                'rejected' => 0,
                'last5' => []
            ],

        ];
        $data['offers']['total'] = CustomerOffer::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->count();
        $offerRequest = OfferRequest::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->get();
        $last5 = OfferRequest::where('customer_id', $customer_id)->where('company_customer_id', $company_id)->orderBy('created_at', 'desc')->limit(5)->get();
        foreach ($last5 as $item) {
            /** @var OfferRequest $item */
            $data['offer_request']['last5'][] = [
                'id' => $item->id,
                'request_no' => $item->request_no,
                'from_store' => $item->from_store,
                'status' => $item->status,
                'totalProduct' => $item->lines()->count(),
                'created_at' => Carbon::parse($item->created_at)->format("d.m.Y H:i:s"),
            ];
        }
        foreach ($offerRequest as $item) {
            if ($item->status == OfferRequestStatusEnum::PENDING->value) {
                $data['offer_request']['pending']++;
            } else if ($item->status == OfferRequestStatusEnum::ACCEPTED->value) {
                $data['offer_request']['accepted']++;
            } else if ($item->status == OfferRequestStatusEnum::REJECTED->value) {
                $data['offer_request']['rejected']++;
            }
        }




        return Responder::send_success("", $data);
    }
}
