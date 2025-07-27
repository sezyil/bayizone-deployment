<?php

namespace App\Http\Controllers\Client\Product;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreProductRequest;
use App\Http\Resources\Client\Product\ProductDetailCollection;
use App\Http\Resources\Client\Product\ProductListCollection;
use App\Libraries\Client\Product\Batch\ProductBatchCore;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\HttpStatusCodes;
use App\Libraries\Visenze\VisenzeCatalogApi;
use App\Models\Customer;
use App\Models\Catalog\Product\Product;
use App\Services\Client\Product\DeleteProductService;
use App\Services\Client\Product\ProductDuplicateService;
use App\Services\Client\Product\RegisterProductService;
use App\Services\Client\Product\UpdateProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Log;

class ProductController extends Controller
{
    private $permissionClass = PermissionTypes::product;
    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index()
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::customer_offer),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::customer_offer),
        ]);
        $products = Customer::find(SanctumHelper::customer_id())->products()->select(
            'id',
            'sku',
            'model',
            'quantity',
            'default_currency',
            'price_tl',
            'price_usd',
            'price_euro',
            'price_gbp',
            'status',
            'volume',
            'package',
            'image',
            'ai_sync'
        );

        $total = 0;
        $limit = DatatableResponder::getLimit(true);
        $current_page = DatatableResponder::getCurrentPage(true);
        if ($limit && $current_page) {
            $products = $products->paginate($limit, ['*'], 'page', $current_page);
            $total = $products->total();
            $products = $products->items();
        } else {
            $products = $products->get();
            $total = $products->count();
        }

        $collection = new ProductListCollection($products);
        return DatatableResponder::sendResponse($collection, $total);
    }

    /**
     * Show the form for creating a new resource.
     *
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreProductRequest  $request
     *
     */
    public function store(StoreProductRequest $request)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $customer_id = $customer->id;
        /* if (!$customer?->activeSubscription?->hasProductCount()) {
            return Responder::send_bad_request('Ürün eklemek için izniniz/limitiniz yok');
        } */
        $input = $request->all();

        $response = RegisterProductService::save($customer_id, $input);

        /* $customer->activeSubscription->decreaseProductCount(1); */

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     */
    public function edit($id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $product = Product::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        $data = new ProductDetailCollection($product);

        return Responder::send_success('', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     */
    public function update(Request $request, $id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $product = Product::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        $validation = new StoreProductRequest();
        $validation->product_id = $id;

        $request->validate($validation->rules(), $validation->messages(), $validation->attributes());

        $input = $request->all();

        return UpdateProductService::save($product, $input);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     */
    public function destroy(int $id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        //call delete service
        $customer_id = SanctumHelper::customer_id();
        $deleteProcess = DeleteProductService::delete($customer_id, $id);

        if ($deleteProcess) {
            /* SanctumHelper::getCustomer()?->activeSubscription?->increaseProductCount(1); */
            return Responder::send_success('Ürün başarıyla silindi');
        } else {
            return Responder::send_response(false, 'Ürün bulunamadı', HttpStatusCodes::HTTP_NOT_FOUND);
        }
    }

    //duplicate product
    public function duplicate(int $id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $customer_id = $customer->id;
        /*  if (!$customer?->activeSubscription?->hasProductCount()) {
            return Responder::send_bad_request('Ürün ekleme limitiniz yok');
        } */
        $product = Product::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();


        $duplicateService = new ProductDuplicateService($customer_id, $id);
        $cloned_product_id = $duplicateService->duplicate();
        if ($cloned_product_id) {
            /* $customer?->activeSubscription?->decreaseProductCount(1); */
            return Responder::send_success('Ürün başarıyla kopyalandı');
        } else {
            return Responder::send_response(false, 'Ürün kopyalanamadı', HttpStatusCodes::HTTP_NOT_FOUND);
        }
    }

    public function syncAi(int $id)
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $customer_id = $customer->id;
        /*  if (!$customer?->activeSubscription?->hasProductCount()) {
            return Responder::send_bad_request('Ürün ekleme limitiniz yok');
        } */
        if (!$customer?->ai_support) {
            return Responder::send_bad_request('Yapay zeka entegrasyonuna sahip değilsiniz');
        }
        $product = Product::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        try {
            $visenzeApi = new VisenzeCatalogApi();
            $req = $visenzeApi->createOrUpdate($product);
            if ($req) {
                $product->ai_sync = true;
                $product->ai_last_sync = now();
                $product->save();
                return Responder::send_success();
            } else {
                return Responder::send_unprocessable('Ürün Eşitlenemedi. Tekrar Deneyiniz. Eğer sorun devam ederse bizimle iletişime geçin.');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return Responder::send_unprocessable('Servise şuan erişilemiyor. Eğer sorun devam ederse bizimle iletişime geçin.');
        }

        /* $duplicateService = new ProductDuplicateService($customer_id, $id);
        $cloned_product_id = $duplicateService->duplicate();
        if ($cloned_product_id) { */
        /* $customer?->activeSubscription?->decreaseProductCount(1); */
        /* return Responder::send_success('Ürün başarıyla kopyalandı');
        } else {
            return Responder::send_response(false, 'Ürün kopyalanamadı', HttpStatusCodes::HTTP_NOT_FOUND);
        } */
    }

    public function batchUpload(Request $request)
    {
        $customer_id = SanctumHelper::customer_id();

        $validator = \Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:csv,json,txt,xlsx,xls',
                'max:10240'
            ]
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("", $validator->errors());
        }

        $file = $request->file('file');
        $fileType = $file->getClientOriginalExtension();

        $productBatch = new ProductBatchCore();
        $file = $productBatch->upload($customer_id, $file);
        $productBatch->addQueue($customer_id, $file, $fileType);
        return Responder::send_success('Ürünler başarıyla yüklendi');
    }

    //autocomplete
    public function autocomplete(Request $request): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        $searchParam = $request->query('q');
        /* $data = Product::where(function (Builder $query) use ($customer_id, $with_default) {
             $is_default = in_array($with_default, [1, 'true', '1']) ? 1 : 0;
             return $query->where('customer_id', $customer_id)
                 ->orWhere('is_default', '=', $is_default);
         })->where('parent_id', '=', 0)->get(); */
        $data = $customer->products()->active()
            ->when($searchParam, function ($query) use ($searchParam) {
                return $query->where('model', 'like', '%' . $searchParam . '%')
                    ->orWhereHas('description', function ($query) use ($searchParam) {
                        $query->where('name', 'like', '%' . $searchParam . '%');
                    });
            })
            ->get()
            ->map(function ($product) {
                /** @var Product $product */
                return [
                    'id' => $product->id,
                    'name' => $product->description->name . ' (' . $product->model . ')',
                ];
            });


        return Responder::send_success("", $data);
    }
}
