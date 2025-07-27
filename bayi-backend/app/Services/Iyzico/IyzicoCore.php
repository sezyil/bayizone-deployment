<?php

namespace App\Services\Iyzico;

class IyzicoCore
{

    private $apiKey;
    private $secretKey;

    private string $iyzicoBaseUrl;

    private string $callBackUrl;

    private \Iyzipay\Options $options;

    public function __construct()
    {
        $this->apiKey = env('IYZICO_API_KEY');
        $this->secretKey = env('IYZICO_SECRET_KEY');
        if (app()->isLocal()) {
            $this->iyzicoBaseUrl = 'https://sandbox-api.iyzipay.com';
        } else {
            $this->iyzicoBaseUrl = 'https://api.iyzipay.com';
        }
        $this->initClass();
    }

    public function getCallBackUrl(): string
    {
        return $this->callBackUrl;
    }


    public function getOptions(): \Iyzipay\Options
    {
        return $this->options;
    }

    private function initClass()
    {
        $this->options = new \Iyzipay\Options();
        $this->options->setApiKey($this->apiKey);
        $this->options->setSecretKey($this->secretKey);
        $this->options->setBaseUrl($this->iyzicoBaseUrl);

        $this->callBackUrl = env('IYZICO_CALLBACK_URL');
    }
}
