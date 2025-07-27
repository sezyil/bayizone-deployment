<?php

namespace App\Services\Iyzico;


use App\Models\Order;
use App\Models\OrderLine;
use App\Models\System\Currency;
use App\Models\Transactions;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use  Iyzipay\Model\Address;

class IyzicoForm extends IyzicoCore
{
    private CreateCheckoutFormInitializeRequest $request;
    private Buyer $buyer;
    private Address $billingAddress;
    private array $basketItems = [];
    private $availableInstallments = [1, 2, 3, 6, 9];
    private Order $order;

    private $errors = [];



    public function __construct(Order $order)
    {
        parent::__construct();
        $this->request = new CreateCheckoutFormInitializeRequest();
        $this->buyer = new Buyer();
        $this->billingAddress = new Address();
        $this->order = $order;
    }

    private function initRequest()
    {
        $request = &$this->request;
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($this->order->id);
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setPaymentSource("WEB");
        $request->setEnabledInstallments($this->availableInstallments);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setPrice($this->convertCurrency($this->order->total));
        $request->setPaidPrice($this->convertCurrency($this->order->total));
        $request->setCallbackUrl($this->getCallBackUrl());
    }

    private function setBuyer()
    {
        $buyer = &$this->buyer;
        $customer = $this->order->customer;
        //id(Buyer)
        $buyer->setId($customer->id);

        $name = explode(" ", $customer->authorized_person);
        //name(Buyer)
        $buyer->setName($name[0]);
        //surname(Buyer)
        $buyer->setSurname($name[1]);
        //identityNumber(Buyer)
        $buyer->setIdentityNumber($customer->tax_no);
        $buyer->setGsmNumber($customer->phone);
        //city(Buyer)
        $buyer->setCity($this->order->invoice_state);
        //country(Buyer)
        $buyer->setCountry($this->order->invoice_country);
        //email(Buyer)
        $buyer->setEmail($customer->email);
        //ip(Buyer)
        $buyer->setIp($this->order->ip_address);
        //registrationAddress(Buyer)
        $registrationAddress = $this->order->invoice_address . " " . $this->order->invoice_state . " " . $this->order->invoice_country;
        $buyer->setRegistrationAddress($registrationAddress);
    }

    private function setBillingAddress()
    {
        $customer = $this->order->customer;
        $billingAddress = &$this->billingAddress;
        $billingAddress->setContactName($customer->firm_name);
        $billingAddress->setCity($this->order->invoice_state);
        $billingAddress->setCountry($this->order->invoice_country);
        $billingAddress->setAddress($this->order->invoice_address);
    }


    private function initBasketItem()
    {
        $orderCoupon = $this->order->coupon ?? null;
        foreach ($this->order->lines as $line) {
            $calculatedtotal = fn ($total) => $total - $orderCoupon->calculateDiscount($total);
            /** @var OrderLine $line */
            $basketItem = new \Iyzipay\Model\BasketItem();
            $basketItem->setId($line->id);
            $basketItem->setName($line->name);
            $basketItem->setCategory1($line->type);
            $basketItem->setCategory2($line->type);
            $basketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
            $basketItem->setPrice($this->convertCurrency($calculatedtotal($line->total)));
            $this->basketItems[] = $basketItem;
        }
    }

    //add error or errors
    private function addError($error)
    {
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }

    private function convertCurrency($amount)
    {
        $converted = Currency::convert($amount, Currency::CODE_USD, Currency::CODE_TL);
        return $converted;
    }


    public function sendRequest(): \Iyzipay\Model\CheckoutFormInitialize
    {
        $this->initRequest();
        $this->setBuyer();
        $this->setBillingAddress();

        $this->initBasketItem();

        $this->request->setBuyer($this->buyer);
        $this->request->setBillingAddress($this->billingAddress);
        $this->request->setBasketItems($this->basketItems);



        $form = \Iyzipay\Model\CheckoutFormInitialize::create($this->request, $this->getOptions());
        return $form;
    }

    public function getRequestObject()
    {
        return $this->request->getJsonObject();
    }
}
