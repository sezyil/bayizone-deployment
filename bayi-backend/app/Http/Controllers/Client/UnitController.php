<?php

namespace App\Http\Controllers\Client;

use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\System\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    private $permissionClass = PermissionTypes::unit;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass, [
            PermissionOperationTypes::UPDATE->generatePermission(PermissionTypes::product),
            PermissionOperationTypes::CREATE->generatePermission(PermissionTypes::product),
        ]);
        $customer_id = SanctumHelper::customer_id();
        $units = Unit::where('customer_id', $customer_id)->orWhere('is_system', true)->get()->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->description?->name ?? $unit->short_name,
                'short_name' => $unit->short_name,
                'is_active' => $unit->is_active,
                'is_system' => $unit->is_system
            ];
        });
        return DatatableResponder::sendResponse($units, $units->count());
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
        $validator = \Validator::make($request->all(), [
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
            'short_name' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ], [
            'name.required' => 'Birim adı zorunludur',
            'name.tr.required' => 'Birim Türkçe adı zorunludur',
            'name.en.required' => 'Birim İngilizce adı zorunludur',
            'short_name.required' => 'Kısa adı zorunludur',
            'is_active.required' => 'Aktiflik durumu zorunludur'
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("", $validator->errors());
        }

        $unit = new Unit();
        $unit->name = $request->name;
        $unit->short_name = $request->short_name;
        $unit->is_active = $request->is_active;
        $unit->customer_id = $customer_id;
        $unit->save();

        return Responder::send_success("Unit created successfully", $unit);
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
        /** @var Unit $unit */
        $unit = Unit::where('customer_id', $customer_id)->where('is_system', false)->findOrFail($id);
        $data = $unit->toArray();
        $data['name']['tr'] = $unit->description('tr')->first()?->name ?? $unit->short_name;
        $data['name']['en'] = $unit->description('en')->first()?->name ?? $unit->short_name;
        return Responder::send_success("", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $validator = \Validator::make($request->all(), [
            "name.tr" => 'required|string',
            "name.en" => 'required|string',
            'short_name' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ], [
            'name.required' => 'Birim adı zorunludur',
            'name.tr.required' => 'Birim Türkçe adı zorunludur',
            'name.en.required' => 'Birim İngilizce adı zorunludur',
            'short_name.required' => 'Kısa adı zorunludur',
            'is_active.required' => 'Aktiflik durumu zorunludur'
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("", $validator->errors());
        }

        /** @var Unit $unit */
        $unit = Unit::where('customer_id', $customer_id)->where('is_system', false)->findOrFail($id);
        $unit->short_name = $request->short_name;
        $unit->is_active = $request->is_active;
        $unit->descriptions()->delete();
        $createData = [];
        foreach ($request->name as $key => $value) {
            $createData[] = ['name' => $value, 'language' => $key];
        }
        $unit->descriptions()->createMany($createData);
        $unit->save();

        return Responder::send_success("Birim başarıyla güncellendi", $unit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $unit = Unit::where('customer_id', $customer_id)->where('is_system', false)->findOrFail($id);
        //check is using in product
        if ($unit->products()->count() > 0) {
            return Responder::send_unprocessable("Bu birim ürünlerde kullanıldığı için silinemez");
        }
        $unit->delete();
        return Responder::send_success("Birim başarıyla silindi");
    }
}
