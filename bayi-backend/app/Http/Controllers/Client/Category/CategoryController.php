<?php

namespace App\Http\Controllers\Client\Category;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Models\Customer;
use App\Models\Catalog\Category\Categories;
use App\Models\Catalog\Category\CategoryDescription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Str;
use Validator;

class CategoryController extends Controller
{
    private $permissionClass = PermissionTypes::category;
    public function index(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::product),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::product),
        ]);
        $customer_id = SanctumHelper::customer_id();
        $page = $request->query('current_page', 1);
        $limit = $request->query('limit', 10);
        $name = $request->query('name', null);
        $uploader = $request->query('uploader', null);

        $data = Categories::where(function (Builder $query) use ($customer_id) {
            return $query->where('customer_id', $customer_id)
                ->orWhere('is_default', '=', true);
        })
            ->when($uploader, function ($query, $uploader) {
                if ($uploader == 'customer') {
                    return $query->where('customer_id', SanctumHelper::customer_id());
                } elseif ($uploader == 'system') {
                    return $query->where('is_default', true);
                } else {
                    return $query;
                }
            })
            ->where('parent_id', '=', 0)->get();

        $formattedData = Categories::Multilevel($data, '', 0);

        if ($name) {
            $name = Str::lowerTurkish($name);
            $formattedData = array_values(array_filter($formattedData, fn ($e) => str_contains(Str::lowerTurkish($e['name']), $name)));
        }

        if (!$formattedData) {
            $formattedData = [];
        }

        $paginatedData = array_slice($formattedData, ($page - 1) * $limit, $limit);



        return DatatableResponder::sendResponse($paginatedData, count($formattedData));
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

        $existData = Categories::where(function (Builder $query) use ($customer_id) {
            return $query->where('customer_id', $customer_id);
        })->get('id');

        $validator = Validator::make($input, [
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
            "parent_id" => [
                "required",
                "numeric",
                Rule::in([...$existData->pluck('id'), ...[0]])
            ]
        ], [
            "name.tr.required" => "Türkçe Adı Giriniz",
            "name.en.required" => "İngilizce Adı Giriniz",
            "parent_id.numeric" => "Geçersiz Kategori ID",
            "parent_id.required" => "Üst Kategori Seçiniz",
            "parent_id.in" => "Bu kendi kategorisine ait bir alt kategori olamaz."
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {
            $newData = Categories::create([
                'customer_id' => $customer_id,
                'parent_id' => $input['parent_id']
            ]);

            $newData->save();

            $newData_id = $newData->id;

            foreach ($input['name'] as $key => $value) {
                $newData->descriptions()->create([
                    'category_id' => $newData_id,
                    'language' => $key,
                    'name' => $value
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
        $data = Categories::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->with('descriptionsLang')->firstOrFail();

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

        $item = Categories::where([
            ['id', '=', $id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();


        $existData = Categories::where(function (Builder $query) use ($customer_id) {
            return $query->where('customer_id', $customer_id);
        })->whereNotIn('id', [$id])->get('id');


        $validator = Validator::make($input, [
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
            "parent_id" => [
                "numeric",
                "required",
                Rule::in([...$existData->pluck('id'), ...[0]])
            ],
        ], [
            "name.tr.required" => "Türkçe Adı Giriniz",
            "name.en.required" => "İngilizce Adı Giriniz",
            "parent_id.numeric" => "Geçersiz Kategori ID",
            "parent_id.required" => "Üst Kategori Seçiniz",
            "parent_id.in" => "Bu kendi kategorisine ait bir alt kategori olamaz."
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {
            $item->parent_id = $input['parent_id'];
            $item->save();

            $item_id = $item->id;

            foreach ($input['name'] as $key => $value) {
                $item->descriptions()->updateOrCreate(
                    ['category_id' => $item_id, 'language' => $key],
                    ['name' => $value]
                );
            }

            $msg = "Update Success!";
            return Responder::send_success($msg);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $item = Categories::where([
            ['id', '=', (int)$id],
            ['customer_id', '=', $customer_id],
        ])->firstOrFail();

        if ($item->children()->first()) {
            return Responder::send_bad_request("", ["Bu Kategoriye Ait Alt Kategoriler Vardır"]);
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
        $with_default = $request->query('wd', 0);
        $data = Categories::where(function (Builder $query) use ($customer_id, $with_default) {
            $is_default = in_array($with_default, [1, 'true', '1']) ? 1 : 0;
            return $query->where('customer_id', $customer_id)
                ->orWhere('is_default', '=', $is_default);
        })->where('parent_id', '=', 0)->get();

        $formattedData = Categories::Multilevel($data, '', 0);

        if ($searchParam) {
            $searchParam = Str::lowerTurkish($searchParam);
            $formattedData = array_values(array_filter($formattedData, fn ($e) => str_contains(Str::lowerTurkish($e['name']), $searchParam)));
        }
        if (!$formattedData) {
            $formattedData = [];
        }
        return Responder::send_success("", $formattedData);
    }
}
