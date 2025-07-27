<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\Responder;
use App\Mail\Client\OrderCreated;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Plan;
use App\Models\PlanPrice;
use App\Models\Subscription;
use App\Models\System\Cities;
use App\Models\System\Countries;
use App\Models\System\States;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mail;
use Validator;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = Plan::active()->trial(false)->with('prices')->get();
        $plans->makeHidden([
            'created_at',
            'updated_at',
        ])->each(function ($plan) {
            /** @var Plan $plan */
            //add after make hidden, add selected column for frontend
            $plan->monthly_price = $plan->prices->where('month', 1)->first()->price;
            $plan->yearly_price = $plan->prices->where('month', 12)->first()->price;
            //fixed
            $plan->divided_price = round($plan->yearly_price / 12, 2);
            $plan->prices->transform(function ($price) use ($plan) {
                /** @var PlanPrice $price */
                $tmp = [
                    'id' => $price->id,
                    'price' => $price->price,
                    'month' => $price->month,
                    'selected' => $price->isYearly(),
                    'type' => $price->isYearly() ? 'yearly' : 'monthly',
                ];
                return $tmp;
            });
        });

        return Responder::send_success(data: $plans);
    }

    public function show(string $id): JsonResponse
    {
        $price_id = request()->get('price_id');
        if (!$price_id) {
            return Responder::send_not_found('Price id is required');
        }
        //find plan and price id
        $plan = Plan::findOrfail($id);
        //find price_id
        $price = $plan->prices->where('id', $price_id)->firstOrFail();
        //unset prices
        $plan->unsetRelation('prices');
        //add selected column
        $plan->selected_price_id = $price->id;
        $plan->selected_price_price = $price->price;
        $plan->selected_price_duration = $price->month;
        //tax calculate %20
        $plan->selected_price_tax = $price->price * 0.20;
        //total price
        $plan->selected_price_subtotal = $price->price;
        //grand total
        $plan->selected_price_total = $price->price + $plan->selected_price_tax;

        $customer = auth()->user()->customer;

        $customerData = [
            'firm_name' => $customer->firm_name,
            'tax_no' => $customer->tax_no,
            'tax_administration' => $customer->tax_administration,
            'address' => $customer->address,
            'country_id' => $customer->country_id,
            'country' => $customer->country?->name, //add country name
            'state_id' => $customer->state_id,
            'state' => $customer->state?->name, //add state name
            'city_id' => $customer->city_id,
            'city' => $customer->city?->name, //add city name
            'postcode' => $customer->postcode,
            'email' => $customer->email,
            'phone' => $customer->phone,
        ];


        return Responder::send_success(data: [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'selected_price_id' => $plan->selected_price_id,
                'selected_price_price' => $plan->selected_price_price,
                'selected_price_duration' => $plan->selected_price_duration,
                'selected_price_tax' => $plan->selected_price_tax,
                'selected_price_subtotal' => $plan->selected_price_subtotal,
                'selected_price_total' => $plan->selected_price_total,
            ],
            'customer' => $customerData
        ]);
    }

    public function purchase(string $id, Request $request): JsonResponse
    {
        $user = SanctumHelper::getUser();
        /** @var Customer $customer */
        $customer = $user->customer;

        if ($customer->activeSubscription && !$customer->activeSubscription->isTrial()) {
            return Responder::send_unprocessable('Aktif aboneliğiniz bulunmaktadır. Paket Yükseltme işlemi için tarafımızla iletişime geçiniz.');
        }

        $plan = Plan::whereIsActive(1)->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'price_id' => [
                'required',
                'integer',
                'exists:plan_prices,id',
                function ($attribute, $value, $fail) use ($plan) {
                    if ($plan->prices->where('id', $value)->count() == 0) {
                        $fail('Fiyat bulunamadı');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable(errors: $validator->errors());
        }

        $price = PlanPrice::wherePlanId($plan->id)->whereId($request->get('price_id'))->firstOrFail();

        $taxRate = 0.20;
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

        //line create
        $itemData = ['duration' => $price->month];
        $order->lines()->create([
            'type' => OrderLine::TYPE_SUBSCRIPTION,
            'name' => $plan->name,
            'price' => $price->price,
            'item_id' => $plan->id,
            'item_data' => $itemData,
            'tax_rate' => $taxRate,
            'tax_amount' => $price->price * $taxRate,
            'quantity' => 1,
            'subtotal' => $price->price,
            'total' => $price->price + $price->price * $taxRate,
        ]);

        //calculate tax and total
        $order->calculatePrices();

        $mail = (new OrderCreated($order))->onQueue('mail_job');
        Mail::to($user)->queue($mail);

        return Responder::send_success(data: [
            'order_id' => $order->id,
        ]);
    }
}
