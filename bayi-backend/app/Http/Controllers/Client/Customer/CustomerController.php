<?php

namespace App\Http\Controllers\Client\Customer;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Enums\SubscriptionTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CompanyUpdateRequest;
use App\Http\Resources\Client\Company\CompanyDetailCollection;
use App\Libraries\Client\FileManager\CustomerFileManager;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\Responder;
use App\Mail\Client\OrderCreated;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\PlanAddon;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mail;

class CustomerController extends Controller
{
    private $permissionClass = PermissionTypes::customer;
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customer = Customer::findOrFail($customer_id);
        $customer = new CompanyDetailCollection($customer);

        return Responder::send_success("", $customer);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyUpdateRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customer = Customer::find($customer_id);
        $formData = $request->validated();
        //city_id or state_id if 0 set null
        $formData['city_id'] = $formData['city_id'] == 0 ? null : $formData['city_id'];
        $formData['state_id'] = $formData['state_id'] == 0 ? null : $formData['state_id'];


        $customer->update($formData);

        $trial_activated = false;
        $customer->refresh();
        if (!$customer->wizard_completed) {
            if ($customer->checkWizardCompleted()) {
                $trial_activated = $customer->activateTrial();
            }
        }

        return Responder::send_success("Firma bilgileri güncellendi.", [
            'trial_activated' => $trial_activated
        ]);
    }

    //customer image update
    public function imageUpdate(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("Resim yüklenemedi.", $validator->errors());
        }
        $customerFileManager = new CustomerFileManager($customer_id);
        $customer = Customer::find($customer_id);
        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->move($customerFileManager->getCustomerProfileImageFolder(), $imageName);
        $customer->image = $customerFileManager->getCustomerProfileImageFolder($imageName);
        $customer->save();

        $customer->refresh();
        $trial_activated = false;

        if (!$customer->wizard_completed) {
            if ($customer->checkWizardCompleted()) {
                $trial_activated = $customer->activateTrial();
            }
        }

        return Responder::send_success("Resim yüklendi.", [
            'trial_activated' => $trial_activated
        ]);
    }


    //get subscription info
    public function services(): JsonResponse
    {
        /** @var User $user */
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;
        $available_addons = [];
        $subscription = $customer->activeSubscription?->makeHidden([
            'customer_id',
            'plan_id',
            'order_id',
            'is_trial',
            'trial_ends_at',
            'canceled_at',
            'created_at',
            'updated_at',
        ]) ?? null;
        if ($subscription && !$subscription->isTrial()) {
            $left_month = $subscription->getLeftMonths();
            $addons = PlanAddon::whereStatus(true)->get();
            foreach ($addons as $addon) {
                if (isset($subscription->{$addon->type}) && $subscription->{$addon->type} !== true) {
                    $_addon = $addon->makeHidden([
                        'status',
                        'created_at',
                        'updated_at',
                    ]);
                    $monthly_price = $addon->price;
                    $last_price = $addon->price;
                    if ($_addon->is_boolean) {
                        $last_price = $addon->price * $left_month;
                    }
                    $available_addons[] = [
                        ...$_addon->toArray(),
                        'monthly_price' => $monthly_price,
                        'last_price' => $last_price,
                    ];
                }
            }
            $subscription['left_month'] = $left_month;
        }



        $response = [
            'subscription' => $subscription,
            'has_active_subscription' => $subscription !== null,
            'available_addons' => $available_addons
        ];


        return Responder::send_success("", $response);
    }


    //update services
    public function updateServices(Request $request): JsonResponse
    {
        $user = SanctumHelper::getUser();
        $customer = $user->customer;
        $subscription = $customer->activeSubscription;
        if (!$subscription) {
            return Responder::send_unprocessable("Abonelik bulunamadığı için hizmetler güncellenemedi.");
        } else if ($subscription->isTrial()) {
            return Responder::send_unprocessable("Deneme süresi devam ettiği için hizmetler güncellenemedi.");
        }

        $validator = Validator::make($request->all(), [
            'addons' => 'required|array',
            'addons.*.type' => 'required|string|exists:plan_addons,type|distinct',
            'addons.*.quantity' => 'required|integer',
            //if type is user_count, quantity must be greater than 0
            'addons.*.amount' => 'numeric|min:1',
        ], [
            'addons.*.type.exists' => 'Geçersiz hizmet türü.',
            'addons.*.quantity.integer' => 'Hizmet miktarı tam sayı olmalıdır.',
            'addons.*.amount.numeric' => 'Hizmet miktarı sayısal olmalıdır.',
            'addons.*.amount.min' => 'Hizmet miktarı sıfırdan büyük olmalıdır.',
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("Hizmetler güncellenemedi.", $validator->errors());
        }

        $addons = $request->get('addons');

        //check subscription permission for duplicate addons
        $tmpError = array_map(function ($addon) use ($subscription) {
            return $subscription->{$addon['type']} ? SubscriptionTypesEnum::description($addon['type']) : null;
        }, array_filter($addons, function ($addon) use ($subscription) {
            return isset($subscription->{$addon['type']}) && $subscription->{$addon['type']} === true;
        }));
        if (count($tmpError) > 0) {
            $msg = "Bu hizmetler zaten abonelikte bulunmaktadır: ";
            $msg .= implode(", ", $tmpError);
            return Responder::send_unprocessable($msg);
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'is_active' => true,
            'invoice_firm_name' => $customer->firm_name,
            'invoice_tax_no' => $customer?->tax_no ?? '',
            'invoice_tax_administration' => $customer?->tax_administration ?? '',
            'invoice_address' => $customer?->address ?? '',
            'invoice_country_id' => $customer?->country_id ?? '',
            'invoice_country' => $customer?->country?->name ?? '',
            'invoice_state_id' => $customer?->state_id ?? '',
            'invoice_state' =>   $customer?->state?->name ?? '',
            'invoice_city_id' => $customer?->city_id ?? '',
            'invoice_city' => $customer?->city?->name ?? '',
            'invoice_postcode' => $customer?->postcode ?? '',
            'invoice_email' => $customer?->email ?? '',
            'invoice_phone' => $customer?->phone ?? '',
        ]);
        $left_month = $subscription->getLeftMonths();
        foreach ($addons as $addon) {
            $planAddon = PlanAddon::whereType($addon['type'])->first();
            //amount is quantity for non boolean addons
            $amount = $addon['amount'] ?? 1;
            $quantity = $addon['quantity'];
            $price = $planAddon->price;
            //if boolean
            if ($planAddon->is_boolean) {
                $amount = 1;
                $quantity = $left_month;
            }
            $subtotal = (float) $price * $quantity * $amount;
            $tax_amount = (float) $subtotal * 0.20;
            $total = $subtotal + $tax_amount;
            $order->lines()->create([
                'type' => OrderLine::TYPE_SUBSCRIPTION_ADDON,
                'name' => $planAddon->name,
                'price' => $price,
                'item_id' => $planAddon->id,
                'item_data' => [
                    'type' => $planAddon->type,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'is_boolean' => $planAddon->is_boolean,
                    "is_bulk" => $planAddon->is_bulk,
                    "bulk_quantity" => $planAddon->bulk_quantity,
                ],
                'tax_rate' => 0.20,
                'quantity' => $quantity * $amount,
                'tax_amount' => $tax_amount,
                'subtotal' =>  $subtotal,
                'total' =>  $total,
            ]);
        }

        $order->calculatePrices();

        $mail = (new OrderCreated($order))->onQueue('mail_job');
        Mail::to($user)->queue($mail);


        return Responder::send_success("Hizmetler güncellendi.", [
            'order_id' => $order->id,
        ]);
    }
}
