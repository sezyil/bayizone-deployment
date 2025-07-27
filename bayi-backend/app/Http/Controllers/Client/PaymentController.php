<?php

namespace App\Http\Controllers\Client;

use App\Enums\OrderPaymentMethods;
use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Mail\Client\OrderCreated;
use App\Mail\Client\OrderSuccess;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\System\Currency;
use App\Models\System\PaymentLog;
use App\Models\System\SystemSetting;
use App\Models\User;
use App\Rules\CityRule;
use App\Rules\CountryRule;
use App\Rules\StateRule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;
use Validator;

class PaymentController extends Controller
{
    private $permissionClass = PermissionTypes::payment;

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $page = request()->get('current_page') ?? 1;
        $limit = request()->get('limit') ?? 10;
        /** @var User $user */
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;
        $data = $customer
            ->orders()
            ->select([
                'id',
                'order_no',
                'total',
                'is_paid',
                'is_active',
                'created_at',
            ])
            ->paginate($limit, ['*'], 'page', $page);
        //hide some columns
        $data->makeHidden(['customer_id', 'created_at', 'updated_at']);
        return DatatableResponder::sendResponse($data->items(), $data->total());
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }


    public function purchase(string $id, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;
        //find Order
        /** @var Order $order */
        $order = $customer->orders()->whereId($id)->whereIsActive(true)->firstOrFail();
        $validator = Validator::make($request->all(), [
            'invoice_firm_name' => 'required',
            'invoice_tax_no' => 'required',
            'invoice_tax_administration' => 'required',
            'invoice_address' => 'required',
            'invoice_country_id' => [new CountryRule],
            'invoice_state_id' => [new StateRule($request->invoice_country_id)],
            'invoice_city_id' => [new CityRule($request->invoice_country_id, $request->invoice_state_id)],
            'invoice_email' => 'required|email',
            'invoice_phone' => 'required',
        ], [
            'invoice_country_id.exists' => 'Ülke bulunamadı',
            'invoice_state_id.exists' => 'İl bulunamadı',
            'invoice_city_id.exists' => 'İlçe bulunamadı',
            'invoice_email.email' => 'Geçerli bir e-posta adresi giriniz',
            'invoice_email.required' => 'E-posta adresi giriniz',
            'invoice_phone.required' => 'Telefon numarası giriniz',
            'invoice_firm_name.required' => 'Firma adı giriniz',
            'invoice_tax_no.required' => 'Vergi numarası giriniz',
            'invoice_tax_administration.required' => 'Vergi dairesi giriniz',
            'invoice_address.required' => 'Adres giriniz',
            'invoice_country_id.required' => 'Ülke seçiniz',
            'invoice_state_id.required' => 'İl seçiniz',
            'invoice_city_id.required' => 'İlçe seçiniz',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable(errors: $validator->errors());
        }

        if (!env('IYZICO_API_KEY')) {
            return Responder::send_success('Şu anda ödeme alınamamaktadır.');
        }


        //set state and city if 0 null
        $request->merge([
            'invoice_state_id' => $request->invoice_state_id ?? 0,
            'invoice_city_id' => $request->invoice_city_id ?? 0,
        ]);

        //checke exist order
        if ($order->is_paid) {
            return Responder::send_unprocessable(msg: 'Bu sipariş zaten ödenmiş.');
        }

        $order->update($validator->validated());

        $iyzicoForm = new \App\Services\Iyzico\IyzicoForm($order);
        $iyziResponse = $iyzicoForm->sendRequest();

        $paymentLog = new PaymentLog();
        $paymentLog->order_id = $order->id;
        $paymentLog->payment_id = $iyziResponse->getConversationId();
        $paymentLog->payment_request = $iyzicoForm->getRequestObject();
        $paymentLog->payment_response = $iyziResponse->getRawResult();
        $paymentLog->payment_token = $iyziResponse->getToken();
        $paymentLog->payment_status = 'iyzico-redirect-' . $iyziResponse->getStatus();
        $paymentLog->payment_error_code = $iyziResponse->getErrorCode();
        $paymentLog->payment_error_message = $iyziResponse->getErrorMessage();
        $paymentLog->save();

        $currencyConverter = fn ($amount) => Currency::convert($amount, Currency::CODE_USD, Currency::CODE_TL);

        if ($iyziResponse->getStatus() == 'success') {
            $updateData = [
                'payment_token' => $iyziResponse->getToken(),
                'converted_total' => $currencyConverter($order->total),
                'converted_tax_amount' => $currencyConverter($order->tax_amount),
                'converted_subtotal' => $currencyConverter($order->subtotal),
            ];
            $response = [
                'payment_disallowed' => true,
                'iyzico_redirect' => null,
            ];

            $response['payment_disallowed'] = false;
            $response['iyzico_redirect'] = $iyziResponse->getPaymentPageUrl();

            $order->update($updateData);
            return Responder::send_success('Ödeme Sayfasına Yönlendiriliyorsunuz.', $response);
        } else {
            return Responder::send_unprocessable('Hata oluştu. Lütfen tekrar deneyiniz.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        //find Order
        /** @var User $user */
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;

        /** @var Order $order */
        $order = $customer->orders()->whereId($id)->firstOrFail();
        //created_at
        $order->makeHidden([
            'customer_id',
            'user_id',
            'ip_address',
        ]);
        $order->lines->setHidden(['order_id', 'created_at', 'updated_at']);
        $order = $order->toArray();
        $order['created_at'] = Carbon::parse($order['created_at'])->format('d.m.Y H:i:s');

        return Responder::send_success(data: $order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        //find Order
        /** @var User $user */
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;

        /** @var Order $order */
        $order = $customer->orders()->whereId($id)->whereIsActive(true)->firstOrFail();
        $order->created_at->format('Y-m-d H:i:s');
        //created_at
        $order->makeHidden([
            'customer_id',
            'user_id',
            'ip_address',
        ]);
        $order->coupon_code = $order->coupon?->code ?? null;
        $order->lines->setHidden(['order_id', 'created_at', 'updated_at']);

        return Responder::send_success(data: $order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }

    //payment info
    public function availableBankTransfer(): JsonResponse
    {
        $info = SystemSetting::get('bank_transfer_detail');
        if (!$info) {
            $info = 'Ödeme bilgileri henüz eklenmemiştir.';
        }
        return Responder::send_success(data: $info);
    }

    //update payment_method
    public function updatePaymentMethod(Request $request, string $id): JsonResponse
    {
        //find Order
        /** @var User $user */
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;

        /** @var Order $order */
        $order = $customer->orders()->whereId($id)->whereIsActive(true)->firstOrFail();
        if ($order->is_paid) {
            return Responder::send_unprocessable('Bu sipariş zaten ödenmiş.');
        }
        if ($order->payment_method == OrderPaymentMethods::BANK_TRANSFER->value && $order->waiting_transfer_approve) {
            return Responder::send_unprocessable('Banka havalesi ödeme yöntemi için onay bekleniyor. Ödeme bilgilerini güncelleyemezsiniz.');
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:' . implode(',', OrderPaymentMethods::all()),
        ], [
            'payment_method.required' => 'Ödeme yöntemi seçiniz',
            'payment_method.in' => 'Geçersiz ödeme yöntemi',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable(errors: $validator->errors());
        }

        $order->update($validator->validated());

        $order->registerCoupon(null);

        return Responder::send_success('Ödeme yöntemi güncellendi.');
    }

    //payment notification
    public function paymentNotification(Request $request, string $id): JsonResponse
    {
        $user = SanctumHelper::getUser();
        $order = $user->customer->orders()
            ->whereId($id)
            ->whereIsActive(true)
            ->whereIsPaid(false)
            ->wherePaymentMethod(OrderPaymentMethods::BANK_TRANSFER->value)

            ->firstOrFail();

        if ($order->waiting_transfer_approve) {
            return Responder::send_unprocessable('Banka havalesi ödeme yöntemi için onay bekleniyor.');
        }
        //for bank_transfer
        $validator = Validator::make($request->all(), [
            'transfer_account_name' => 'required',
            'transfer_bank_name' => 'required',
            'transfer_reference_no' => 'required',
            'transfer_date' => 'required|date',
        ], [
            'transfer_account_name.required' => 'Hesap adı giriniz',
            'transfer_bank_name.required' => 'Banka adı giriniz',
            'transfer_reference_no.required' => 'Referans numarası giriniz',
            'transfer_date.required' => 'Tarih giriniz',
            'transfer_date.date' => 'Geçerli bir tarih giriniz',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable(errors: $validator->errors());
        }

        $data = [
            ...$validator->validated(),
            'waiting_transfer_approve' => true,
        ];

        $order->update($data);

        return Responder::send_success('Ödeme bilgileri alındı. Onay bekleniyor.');
    }

    //apply coupon
    public function applyCoupon(Request $request, string $id): JsonResponse
    {
        $user = SanctumHelper::getUser();
        /** @var Order $order */
        $order = $user->customer->orders()
            ->whereId($id)
            ->whereIsActive(true)
            ->whereIsPaid(false)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'coupon' => 'required',
        ], [
            'coupon.required' => 'Kupon kodu giriniz',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable(errors: $validator->errors());
        }

        if (!$order->isYearlyAndSubscription()) {
            return Responder::send_unprocessable('Kupon sadece yıllık aboneliklerde kullanılabilir.');
        }

        /** @var Coupon $coupon */
        $coupon = Coupon::active()->notExpired()->whereCode($validator->validated()['coupon'])->first();
        if (!$coupon) {
            return Responder::send_unprocessable('Kupon kodu geçersiz.');
        }

        /* check limit has */
        if ($coupon->isLimitReached()) {
            return Responder::send_unprocessable('Kupon limiti dolmuştur.');
        }

        if ($coupon->isOnlyCash() && $order->payment_method != OrderPaymentMethods::BANK_TRANSFER->value) {
            return Responder::send_unprocessable('Bu kupon sadece banka havalesi ödeme yöntemi için geçerlidir.');
        }

        if ($coupon->isCustomerBased() && !$coupon->customers->contains($user->customer)) {
            return Responder::send_unprocessable('Bu kupon kodunu kullanamazsınız.');
        }




        $order = $order->registerCoupon($coupon);



        if ($order->coupon_id) {
            return Responder::send_success('Kupon uygulandı.');
        } else {
            return Responder::send_unprocessable('Kupon kodu geçersiz.');
        }
    }
}
