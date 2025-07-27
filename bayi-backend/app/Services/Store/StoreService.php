<?php

namespace App\Services\Store;

use App;
use App\Exceptions\StoreServiceException;
use App\Models\Catalog\Product\Product;
use App\Models\Customer;
use App\Models\StoreUser;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Str;

class StoreService
{
    const HEADERSTOREUSERKEY = 'user-uuid';
    private Customer $customer;
    private StoreUser $storeUser;
    private ProductService $productService;
    private CartService $cartService;
    private OfferService $offerService;

    public function __construct(string $customer_id)
    {
        try {
            $this->setLanguage();
            $this->setCustomer($customer_id);
            $this->setStoreUserIdFromHeader();
        } catch (\Exception $e) {
            throw new StoreServiceException($e->getMessage());
        }

        $this->productService = new ProductService($this->customer, $this->storeUser);
        $this->cartService = new CartService($this->customer, $this->storeUser);
        $this->offerService = new OfferService($this->customer, $this->storeUser);
    }

    private function setLanguage(): void
    {
        $language = request()->header('Accept-Language', 'tr'); // 'en', 'tr'
        $availableLanguages = ['en', 'tr'];
        if (!in_array($language, $availableLanguages)) {
            $language = 'tr';
        }
        App::setLocale($language);
    }


    private function setCustomer(string $customer_id): void
    {
        try {
            $customer = Customer::findOrFail($customer_id);
            $this->customer = $customer;
            if (!$customer->status) throw new \Exception('Customer is not active');
            if (!$customer?->activeSubscription?->online_store) throw new \Exception('Online store is not active');
        } catch (\Exception $e) {
            throw new \Exception('Customer not found');
        }
    }

    private function setStoreUserIdFromHeader(): void
    {
        $storeUserKey = request()->header(self::HEADERSTOREUSERKEY);
        if (!$storeUserKey) {
            throw new \Exception("User Header not found");
        }
        $this->createUser($storeUserKey);
    }

    private function createUser(string $userKey): void
    {
        $user = StoreUser::where('user_id', $userKey)
            ->where('customer_id', $this->customer->id)
            ->first();
        if (!$user) {
            $user = StoreUser::create([
                'user_id' => $userKey,
                'customer_id' => $this->customer->id
            ]);
        }
        $this->storeUser = $user;
    }


    public static function init(string $customer_id): self
    {
        return new self($customer_id);
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function storeDetail(): ?array
    {
        $options = new QROptions;
        $options->scale = 5;

        $store_url = env('CLIENT_STORE_URL') . '/' . $this->customer->id;
        $store = [
            'id' => $this->customer->id,
            'name' => Str::title($this->customer->firm_name), //Str::title($customer->name
            'image' => $this->customer->image ? url($this->customer->image) : null,
            'qr_code' => (new QRCode($options))->render($store_url),
            'ai_support' => $this->customer->ai_support,
            'ai_catalog_id' => $this->customer->ai_catalog_id
        ];
        return $store;
    }

    public function productService(): ProductService
    {
        return $this->productService;
    }

    public function cartService(): CartService
    {
        return $this->cartService;
    }

    public function offerService(): OfferService
    {
        return $this->offerService;
    }
}
