<?php

namespace App\Http\Controllers\Client\Attributes;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Models\Customer;
use App\Models\Catalog\Attribute\AttributeGroup;
use App\Models\Catalog\Attribute\AttributeGroupDescription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\JsonResponse;
use Validator;

class AttributeGroupController extends Controller
{
    private $permissionClass = PermissionTypes::attribute_group;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::product),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::product),
        ]);

        /** @var Customer $customer */
        $customer = SanctumHelper::getCustomer();
        $limit = $request->query('limit', 10);
        $current_page = $request->query('current_page', 1);
        $data = $customer->attributeGroups()->orWhere('is_default', '=', true)
            ->with('description')
            ->paginate($limit, ['*'], 'page', $current_page);
        return DatatableResponder::sendResponse($data->items(), $data->total());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->all();

        $validator = Validator::make($input, [
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
        ], [
            "name.tr.required" => "Türkçe isim alanı zorunludur",
            "name.en.required" => "İngilizce isim alanı zorunludur",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {
            $attributeGroup = Customer::find(SanctumHelper::customer_id())->attributeGroups()->create();

            $attributeGroup_id = $attributeGroup->id;

            foreach ($input['name'] as $key => $value) {
                $attributeGroup->descriptions()->create([
                    'name' => $value,
                    'language' => $key,
                ]);
            }

            $msg = "Register Success!";
            return Responder::send_success($msg);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $data = AttributeGroup::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        $response = [
            'id' => $data->id,
            'name' => [
                'tr' => $data->descriptions->where('language', '=', 'tr')->first()?->name ?? '',
                'en' => $data->descriptions->where('language', '=', 'en')->first()?->name ?? '',
            ]
        ];

        return Responder::send_success('', $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $item = AttributeGroup::whereCustomerId($customer_id)->findOrFail($id);
        $response = Responder::send_bad_request("Error!");
        if ($item) {
            $input = $request->all();
            $validator = Validator::make($input, [
                "name.tr" => 'required|string',
                "name.en" => 'required|string',
            ], [
                "name.tr.required" => "Türkçe isim alanı zorunludur",
                "name.en.required" => "İngilizce isim alanı zorunludur",
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response = Responder::send_unprocessable("", $errors);
            } else {
                //update tr and en
                $item->descriptions()->where('language', 'tr')->update(['name' => $input['name']['tr']]);
                $item->descriptions()->where('language', 'en')->update(['name' => $input['name']['en']]);

                $msg = "Register Success!";
                $response = Responder::send_success($msg);
            }
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $item = AttributeGroup::where([
            ['id', '=', (int)$id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        //Bağlı özellik varsa silinemez
        if ($item->attributes()->first()) {
            return Responder::send_bad_request("", ["Bu Özellik Grubuna Ait Özellikler Vardır Silemezsiniz"]);
        } else {
            $item->delete();
            return Responder::send_success("Success!");
        }
    }

    //autocomplete
    public function autocomplete(Request $request): JsonResponse
    {
        $customer_id = SanctumHelper::customer_id();
        $searchParam = $request->query('q');
        $data = AttributeGroup::where(function (Builder $query) use ($customer_id) {
            return $query->where('customer_id', $customer_id)
                ->orWhere('is_default', '=', true);
        })->withWhereHas('description', function ($query) use ($searchParam) {
            if ($searchParam) $query->where('name', 'LIKE', "%{$searchParam}%");
            $query->where('language', SanctumHelper::userLanguage());
        })->get();
        return Responder::send_success('', $data);
    }
}
