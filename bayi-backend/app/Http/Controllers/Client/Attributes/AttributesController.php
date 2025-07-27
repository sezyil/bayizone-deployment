<?php

namespace App\Http\Controllers\Client\Attributes;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Models\Catalog\Attribute\AttributeDescription;
use App\Models\Catalog\Attribute\AttributeGroup;
use App\Models\Catalog\Attribute\Attributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\JsonResponse;
use Validator;

class AttributesController extends Controller
{
    private $permissionClass = PermissionTypes::attribute;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::product),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::product),
        ]);
        $customer_id = SanctumHelper::customer_id();
        $limit = $request->query('limit', 10);
        $current_page = $request->query('current_page', 1);

        $data = Attributes::where(function (Builder $query) use ($customer_id,) {
            return $query->where('customer_id', $customer_id)
                ->orWhere('is_default', '=', true);
        })->with(['description', 'groupDescription'])->paginate($limit, ['*'], 'page', $current_page);
        return DatatableResponder::sendResponse($data->items(), $data->total());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->all();


        $existData = AttributeGroup::where(function (Builder $query) use ($customer_id) {
            return $query->where('customer_id', $customer_id)
                ->orWhere('is_default', '=', true);
        })->get();

        $validator = Validator::make($input, [
            "attribute_group_id" => [
                "required",
                "numeric",
                Rule::in($existData->pluck('id'))
            ],
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
        ], [
            "attribute_group_id.in" => "Geçersiz Grup",
            "name.tr.required" => "Türkçe isim alanı zorunludur",
            "name.en.required" => "İngilizce isim alanı zorunludur",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {
            $item = Attributes::create([
                'customer_id' => $customer_id,
                'attribute_group_id' => $input['attribute_group_id']
            ]);

            $item->save();

            $item_id = $item->id;

            foreach ($input['name'] as $key => $value) {
                $item->descriptions()->create([
                    'attribute_id' => $item_id,
                    'language' => $key,
                    'name' => $value,
                ]);
            }



            $msg = "Kayıt Başarılı!";
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
        $data = Attributes::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->with(['groupDescription', 'descriptions'])->firstOrFail();

        return Responder::send_success('', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->all();

        $item = Attributes::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        $existData = AttributeGroup::where(function (Builder $query) use ($customer_id) {
            return $query->where('customer_id', $customer_id)
                ->orWhere('is_default', '=', true);
        })->get();

        $validator = Validator::make($input, [
            "attribute_group_id" => [
                "required",
                "numeric",
                Rule::in($existData->pluck('id'))
            ],
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
        ], [
            "attribute_group_id.in" => "Geçersiz Grup",
            "name.tr.required" => "Türkçe isim alanı zorunludur",
            "name.en.required" => "İngilizce isim alanı zorunludur",
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {
            $item->attribute_group_id = $input['attribute_group_id'];
            $item->save();
            $item_id = $item->id;

            foreach ($input['name'] as $key => $value) {
                $item->descriptions()->updateOrCreate([
                    'attribute_id' => $item_id,
                    'language' => $key,
                ], [
                    'name' => $value,
                ]);
            }

            return Responder::send_success("Başarılı!");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $item = Attributes::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        if ($item->hasProduct()->first()) {
            return Responder::send_unprocessable("Bu özellik ürünlerde kullanıldığı için silinemez!");
        } else {
            $item->delete();
            return Responder::send_success("Başarılı!");
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
        })->get();
        return Responder::send_success('', $data);
    }
}
