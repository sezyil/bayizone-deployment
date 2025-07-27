<?php

namespace App\Http\Controllers\Dealer;

use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Enums\Shipment\CustomerShipmentStatusEnum;
use App\Exports\ShipmentExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Shipment\ShipmentAvailableOrdersCollection;
use App\Http\Resources\Client\Shipment\ShipmentListCollection;
use App\Libraries\Client\SanctumDealerHelper;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Mail\CompanyCustomer\CompanyCustomerShipment;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use App\Models\CustomerShipment;
use App\Models\CustomerShipmentHistory;
use App\Models\CustomerShipmentLine;
use App\Models\System\Currency;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customer = SanctumDealerHelper::getCustomer();
        $company_customer_id = request()->get('company_customer_id');

        $limit = DatatableResponder::getLimit();
        $current_page = DatatableResponder::getCurrentPage();

        $shipment_no = request()->get('shipment_no');
        $status = request()->get('status');

        //check status
        $status = $status ? (in_array($status, CustomerShipmentStatusEnum::all()) ? $status : null) : null;
        if ($status == CustomerShipmentStatusEnum::DRAFT) $status = null;

        $data = $customer->shipments()
            ->where('status', '!=', CustomerShipmentStatusEnum::DRAFT)
            ->when($shipment_no, function ($query) use ($shipment_no) {
                return $query->where('shipment_no', 'like', '%' . $shipment_no . '%');
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($company_customer_id, function ($query) use ($company_customer_id) {
                return $query->where('company_customer_id', $company_customer_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $current_page);

        $list = ShipmentListCollection::make($data->items());

        return DatatableResponder::sendResponse($list, $data->total());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer = SanctumDealerHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();

        $data = [
            'id' => $shipment->id,
            'company_customer_id' => $shipment->company_customer_id,
            'company_customer_name' => $shipment->company_customer_name,
            'container_no' => $shipment->container_no,
            'shipment_no' => $shipment->shipment_no,
            'carrier' => $shipment->carrier,
            'currency' => $shipment->currency,
            'note' => $shipment->note,
            'status' => $shipment->status,
            'status_label' => CustomerShipmentStatusEnum::description($shipment->status),
            'total_price' => $shipment->getCurrency?->format($shipment->total_price, true),
            'total_tax' => $shipment->getCurrency?->format($shipment->total_tax, true),
            'total_discount' => $shipment->getCurrency?->format($shipment->total_discount, true),
            'grand_total' => $shipment->getCurrency?->format($shipment->grand_total, true),
            'is_international' => $shipment->is_international,
            'total_volume' => $shipment->total_volume . ' m³',
            'total_package' => $shipment->total_package,
            'total_weight' => $shipment->total_weight,
            'created_at' => $shipment->created_at->format('d.m.Y H:i'),
            'updated_at' => $shipment->updated_at->format('d.m.Y H:i'),
            'shipped_at' => $shipment->shipped_at?->format('d.m.Y') ?? null,
            'delivered_at' => $shipment->delivered_at?->format('d.m.Y') ?? null,
            'lines' => $shipment->lines?->map(function ($line) use ($shipment) {
                /** @var CustomerShipmentLine $line */
                return [
                    'id' => $line->id,
                    'customer_order_line_id' => $line->customer_order_line_id,
                    'order_id' => $line->orderLine->customer_order->id,
                    'order_no' => $line->orderLine->customer_order->order_no,
                    'line_no' => $line->line_no,
                    'quantity' => $line->quantity,
                    'unit_volume' => $line->unit_volume . ' m³',
                    'unit_package' => $line->unit_package,
                    'weight' => $line->weight,
                    'total_volume' => $line->total_volume . ' m³',
                    'total_package' => $line->total_package,
                    'total_weight' => $line->total_weight,
                    'product_name' => $line->orderLine->product_name,
                    'product_code' => $line->orderLine->product_code,
                    'product_image' => $line->orderLine->product_image_url,
                    'unit_price' => $shipment->getCurrency?->format($line->orderLine->unit_price, true),
                    'grand_total' => $shipment->getCurrency?->format($line->orderLine->grand_total, true),
                    'color' => $line->orderLine->variants()->colors()->get()->map(function ($variant) {
                        return $variant->transformData(true, true);
                    }) ?? null,
                    'dimension' => $line->orderLine->variants()->dimensions()->get()->map(function ($variant) {
                        return $variant->transformData(true, true);
                    }) ?? null,
                ];
            }),
            'histories' => $shipment->histories?->map(function ($history) {
                /** @var CustomerShipmentHistory $history */
                return [
                    'id' => $history->id,
                    'status' => $history->status,
                    'status_label' => CustomerShipmentStatusEnum::description($history->status),
                    'note' => $history->note,
                    'created_at' => $history->created_at->format('d.m.Y H:i'),
                    'notify' => $history->notify,
                ];
            }) ?? [],
        ];



        return Responder::send_success(data: $data);
    }


    public function exportToExcel(string $id): JsonResponse|BinaryFileResponse
    {
        $lang = request()->get('lang', 'tr');
        if (!in_array($lang, ['tr', 'en'])) {
            $lang = 'tr';
        }

        $customer_id = SanctumDealerHelper::customer_id();
        $customerShipment = CustomerShipment::where('customer_id', $customer_id)->where('id', $id)->first();
        if ($customerShipment == null) {
            return Responder::send_response(false, 'Customer shipment not found', 404);
        }

        $exportClass = new ShipmentExport($customerShipment, $lang);
        /* return Excel::download($exportClass, 'offer.xlsx'); */
        //api response

        $fileName = $customerShipment->shipment_no . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download($exportClass, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
