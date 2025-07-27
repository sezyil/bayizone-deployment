<?php

namespace App\Services\Iyzico;


use App\Models\Order;

class IyzicoPaymentChecker extends IyzicoCore
{
    private Order $order;
    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }

    public function checkPayment(): \Iyzipay\Model\CheckoutForm
    {
        $token = $this->order->payment_token;
        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($this->order->id);
        $request->setToken($this->order->payment_token);
        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $this->getOptions());


        return $checkoutForm;
    }
}
