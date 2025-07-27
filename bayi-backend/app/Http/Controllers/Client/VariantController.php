<?php

namespace App\Http\Controllers\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Enums\VariantTypesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\VariantRequest;
use App\Http\Resources\Client\VariantCollection;
use App\Http\Resources\Client\VariantResource;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\Variant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VariantController extends Controller
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
        $variants = Variant::active()->defaultAndCustomer($customer->id)->orderBy('created_at', 'desc')->paginate($limit, ['*'], 'page', $page);
        $collection = VariantCollection::make($variants);

        return DatatableResponder::sendResponse($collection, $variants->total());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VariantRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $variant = $customer->variants()->create($request->validated());

        $names = $request->get('names');
        foreach ($names as $k => $v) {
            $variant->descriptions()->create([
                'language' => $k,
                'name' => $v
            ]);
        }


        return Responder::send_success(data: VariantResource::make($variant));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $variant = Variant::findOrFail($id);
        return Responder::send_success(data: VariantResource::make($variant));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VariantRequest $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        /** @var Variant $variant */
        $variant = $customer->variants()->findOrFail($id);
        $variant->update($request->validated());
        $variant->descriptions()->delete();

        $names = $request->get('names');
        foreach ($names as $k => $v) {
            $variant->descriptions()->create([
                'language' => $k,
                'name' => $v
            ]);
        }


        return Responder::send_success(data: VariantResource::make($variant));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $variant = $customer->variants()->findOrFail($id);
        $variant->delete();

        return Responder::send_success();
    }
}
