<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\System\BatchProcesses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BatchProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $customer_id = SanctumHelper::customer_id();
        $type = $request->query('type');
        $limit = $request->query('limit',10);
        $page = $request->query('current_page',1);
        $data = BatchProcesses::where('customer_id', $customer_id)->when($type, function ($query, $type) {
            return $query->where('type', $type);
        })->where('is_system', false)->paginate($limit, ['*'], 'page', $page);

        $count= $data->total();
        $data = collect($data->items())->map(function ($item) {
            /** @var BatchProcesses $item */
            $payload = json_decode($item->payload);
            $errors= $item->errors ? json_decode($item->errors) : [];
            $tmpItem = [
                "id" => $item->id,
                "type" => $item->type,
                "status" => $item->is_completed ? "Tamamlandı" : "Bekliyor",
                "errors" => $errors,
                "has_error" => count($errors) > 0,
                "created_at" => Carbon::parse($item->created_at)->format("d.m.Y H:i:s"),
            ];
            if ($item->type == "PRODUCT") {
                $tmpItem["count"] = $payload->count ?? '---';
            }
            return $tmpItem;
        });

        return DatatableResponder::sendResponse($data, $count);
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
    public function store(Request $request): RedirectResponse
    {
        //
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
    public function edit(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }
}
