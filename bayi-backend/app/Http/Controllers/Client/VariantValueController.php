<?php

namespace App\Http\Controllers\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Enums\VariantTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\VariantValueRequest;
use App\Http\Resources\Client\VariantValueCollection;
use App\Http\Resources\Client\VariantValueResource;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\VariantValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VariantValueController extends Controller
{

    private $permissionClass = PermissionTypes::variant;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::product),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::product),
        ]);
        $customer = SanctumHelper::getCustomer();
        $limit = DatatableResponder::getLimit();
        $page = DatatableResponder::getCurrentPage();
        $variants = VariantValue::defaultAndCustomer($customer->id)->orderBy('created_at', 'desc')->paginate($limit, ['*'], 'page', $page);
        $collection = VariantValueCollection::make($variants);

        return DatatableResponder::sendResponse($collection, $variants->total());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantValueRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $variant = $customer->variantValues()->create([
            'variant_type' => VariantTypesEnum::COLOR->value
        ]);

        $names = $request->validated('names');
        foreach ($names as $k => $v) {
            $variant->descriptions()->updateOrCreate([
                'language' => $k
            ], [
                'name' => $v
            ]);
        }


        return Responder::send_success(data: VariantValueResource::make($variant));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        /** @var VariantValue $variant */
        $variant = $customer->variantValues()->findOrFail($id);

        return Responder::send_success(data: VariantValueResource::make($variant));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VariantValueRequest $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        /** @var VariantValue $variant */
        $variant = $customer->variantValues()->findOrFail($id);


        $names = $request->validated('names');
        foreach ($names as $k => $v) {
            $variant->descriptions()->updateOrCreate([
                'language' => $k
            ], [
                'name' => $v
            ]);
        }

        return Responder::send_success(data: VariantValueResource::make($variant));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        /** @var VariantValue $variant */
        $variant = $customer->variantValues()->findOrFail($id);
        $variant->delete();

        return Responder::send_success();
    }
}
