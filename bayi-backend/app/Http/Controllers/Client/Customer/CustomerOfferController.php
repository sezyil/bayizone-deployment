<?php

namespace App\Http\Controllers\Client\Customer;

use App\Enums\CustomerOfferStatusEnum;
use App\Enums\OfferRequestStatusEnum;
use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Exports\OfferExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CustomerOfferRequest;
use App\Http\Resources\Client\Customer\CustomerOfferListCollection;
use App\Http\Resources\Client\CustomerOfferDetailCollection;
use App\Http\Resources\Client\CustomerOfferPreviewCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Mail\CompanyCustomer\OfferStatusChanged;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOfferLine;
use App\Models\OfferRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerOfferController extends Controller
{
    private $permissionClass = PermissionTypes::customer_offer;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //get data from query company_customer_id,customer_email,currency
        $company_customer_id = request()->get('company_customer_id') ?? null;
        $customer_email = request()->get('customer_email') ?? null;
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


        $data = CustomerOffer::where('customer_id', $customer_id)
            ->when($company_customer_id, function ($query, $company_customer_id) {
                return $query->where('company_customer_id', $company_customer_id);
            })->when($customer_email, function ($query, $customer_email) {
                return $query->where('customer_email', $customer_email);
            })->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })->when($dateFilterType, function ($query, $dateFilterType) use ($firstDate, $secondDate) {
                if ($dateFilterType == 'offer_create_date') {
                    return $query->whereBetween('offer_date', [$firstDate, $secondDate]);
                } else if ($dateFilterType == 'offer_due_date') {
                    return $query->whereBetween('offer_due_date', [$firstDate, $secondDate]);
                }
            })
            ->orderBy('created_at', 'desc');
        $total = 0;
        $limit = DatatableResponder::getLimit(true);
        $current_page = DatatableResponder::getCurrentPage(true);
        if ($limit && $current_page) {
            $data = $data->paginate($limit, ['*'], 'page', $current_page);
            $total = $data->total();
            $data = $data->items();
        } else {
            $data = $data->get();
            $total = $data->count();
        }

        $response = new CustomerOfferListCollection($data);
        return DatatableResponder::sendResponse($response, $total);
    }

    /**
     * Display a listing of the resource. (for company)
     */
    public function list(string $company_customer): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = CustomerOffer::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->get();
        $result = new CustomerOfferListCollection($data);
        return DatatableResponder::sendResponse($result, $result->count());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerOfferRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->validated();
        $detail = $input['detail'];
        $detail['customer_id'] = $customer_id;
        $detail['status'] = CustomerOfferStatusEnum::DRAFT->value;
        $company_id = $detail['company_customer_id'];
        $request_id = $request->get('request_id');
        if ($request_id) {
            $checkRequest = OfferRequest::where('customer_id', $customer_id)
                ->where('company_customer_id', $company_id)
                ->where('id', $request_id)->first();
            if ($checkRequest == null) {
                return Responder::send_unprocessable('Teklif isteği bulunamadı.');
            }
        }
        $lines = $input['lines'];

        $customerOffer = new CustomerOffer();
        $offer_id = $customerOffer->saveOffer($customer_id, $detail['company_customer_id'], $detail);
        if ($offer_id == null) {
            return Responder::send_unprocessable('Teklif oluşturulamadı.');
        }
        if ($request_id) {
            /** @var OfferRequest $checkRequest */
            $checkRequest->customer_offer_id = $offer_id;
            $checkRequest->status = OfferRequestStatusEnum::ACCEPTED->value;

            $checkRequest->save();
        }


        $customerOfferLine = new CustomerOfferLine();
        $customerOfferLine->saveLines($lines, $offer_id);

        return Responder::send_success($customerOffer, 'Teklif oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $lang = request()->get('lang', 'tr');
        if (!in_array($lang, ['tr', 'en'])) {
            $lang = 'tr';
        }
        app()->setLocale($lang);
        $customer_id = SanctumHelper::customer_id();
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('id', $id)->with('lines')->get();
        if ($customerOffer->isEmpty()) {
            return Responder::send_response(false, 'Customer offer not found.',  404);
        }
        $data = new CustomerOfferPreviewCollection($customerOffer->first());
        return Responder::send_success("", $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('id', $id)->with('lines')->get();
        if ($customerOffer->isEmpty()) {
            return Responder::send_response(false, 'Customer offer not found.',  404);
        }
        $data = new CustomerOfferDetailCollection($customerOffer);
        return Responder::send_success("", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerOfferRequest $request, string $company_customer_id, string $offer_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //check offer id
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('id', $offer_id)->firstOrFail();
        $input = $request->validated();
        $detail = $input['detail'];
        $detail['customer_id'] = $customer_id;
        $detail['status'] = CustomerOfferStatusEnum::DRAFT->value;

        $lines = $input['lines'];

        $customerOffer = new CustomerOffer();
        $offer_id = $customerOffer->saveOffer($customer_id, $detail['company_customer_id'], $detail, $offer_id);
        if ($offer_id == null) {
            return Responder::send_unprocessable('Customer offer not created.');
        }

        $customerOfferLine = new CustomerOfferLine();
        $customerOfferLine->saveLines($lines, $offer_id, true);

        return Responder::send_success($customerOffer, 'Customer offer created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company_customer, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        //get param from url customer_offer

        $customer_id = SanctumHelper::customer_id();
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->where('id', $id)->firstOrFail();


        $customerOffer->delete();
        return Responder::send_success('Customer offer deleted successfully.');
    }

    //export to excel
    public function exportToExcel(string $id): JsonResponse|BinaryFileResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);

        $lang = request()->get('lang', 'tr');
        if (!in_array($lang, ['tr', 'en'])) {
            $lang = 'tr';
        }

        $customer_id = SanctumHelper::customer_id();
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('id', $id)->first();
        if ($customerOffer == null) {
            return Responder::send_response(false, 'Customer offer not found.',  404);
        }

        $exportClass = new OfferExport($customerOffer, $lang);
        /* return Excel::download($exportClass, 'offer.xlsx'); */
        //api response

        $fileName = $customerOffer->offer_no . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download($exportClass, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    //status update
    public function statusUpdate(Request $request, string $company_customer, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        //validator status
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required|in:' . implode(',', CustomerOfferStatusEnum::all()),
            'resend' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable($validator->errors());
        }

        $customer_id = SanctumHelper::customer_id();
        $user_id = SanctumHelper::user_id();
        $user = User::find($user_id);
        $user_fullName = $user->getFullName();
        $customerOffer = CustomerOffer::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->where('id', $id)->first();
        if ($customerOffer == null) {
            return Responder::send_response(false, 'Müşteri Bulunamadı.',  404);
        } elseif ($customerOffer->status == $request->status && !$request->resend) {
            return Responder::send_response(false, 'Müşteri teklif durumu zaten ' . __('offer-status.' . $request->status) . '.',  422);
        }

        //status transition
        $statusTransition = [
            CustomerOfferStatusEnum::DRAFT->value => [
                CustomerOfferStatusEnum::PENDING->value,
                CustomerOfferStatusEnum::CANCELED->value,
            ],
            CustomerOfferStatusEnum::PENDING->value => [
                CustomerOfferStatusEnum::APPROVED->value,
                CustomerOfferStatusEnum::REVISED->value,
                CustomerOfferStatusEnum::REJECTED->value,
                CustomerOfferStatusEnum::CANCELED->value,
            ],
            CustomerOfferStatusEnum::APPROVED->value => [
                CustomerOfferStatusEnum::CLOSED->value,
                CustomerOfferStatusEnum::CANCELED->value,

            ],
        ];
        if (!in_array($request->status, $statusTransition[$customerOffer->status]) && !$request->resend) {
            return Responder::send_response(false, 'Müşteri teklif durumu ' . __('offer-status.' . $customerOffer->status) . ' iken ' . __('offer-status.' . $request->status) . ' olarak güncellenemez.',  422);
        }
        $is_new = false;
        $msg = __('offer-status.' . $customerOffer->status) . ' iken ' . __('offer-status.' . $request->status) . ' olarak güncellendi.';
        //if status is pending, check resend param if resend pass send mail for now fake
        if ($request->status == CustomerOfferStatusEnum::PENDING->value) {
            if ($request->resend) {
                $msg = 'Mail tekrar gönderildi.';
            } else {
                $customerOffer->status = CustomerOfferStatusEnum::PENDING->value;
                $msg .= ' Mail gönderildi.';
                $is_new = true;
            }
            $customerOffer->mail_notification_date = now();
        } else if ($request->status == CustomerOfferStatusEnum::CANCELED->value) {
            $customerOffer->status = CustomerOfferStatusEnum::CANCELED->value;
            $customerOffer->cancelled_date = now();
            $customerOffer->cancelled_user_name = $user_fullName;
            $msg .= ' Mail gönderildi.';
            $customerOffer->mail_notification_date = now();
        } else if ($request->status == CustomerOfferStatusEnum::CLOSED->value) {
            $customerOffer->status = CustomerOfferStatusEnum::CLOSED->value;
            $customerOffer->closed_date = now();
            $customerOffer->closed_user_name = $user_fullName;
        }
        $customerOffer->save();
        $mail = (new OfferStatusChanged($customerOffer, $customerOffer->status, $is_new))->onQueue('mail_job');
        Mail::to($customerOffer->company_customer->email, $is_new)->locale(CompanyCustomer::find($company_customer)->language)->queue($mail);

        return Responder::send_success($msg);
    }


    //convert to order
    public function convertToOrder(string $company_customer, string $customer_offer): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customerOffer = CustomerOffer::whereCustomerId($customer_id)
            ->whereCompanyCustomerId($company_customer)
            ->whereId($customer_offer)
            ->whereStatus(CustomerOfferStatusEnum::CLOSED->value)
            ->first();
        if ($customerOffer == null || $customerOffer->customer_order_id != null) {
            return Responder::send_response(false, 'Kayıt bulunamadı. Veya siparişe dönüştürülmüş.',  404);
        }
        /** @var CustomerOffer $customerOffer */
        $order_id = $customerOffer->convertToOrder();
        if ($order_id == null) {
            return Responder::send_unprocessable('Sipariş oluşturulamadı.');
        }
        return Responder::send_success('Sipariş oluşturuldu.', ['order_id' => $order_id]);
    }
}
