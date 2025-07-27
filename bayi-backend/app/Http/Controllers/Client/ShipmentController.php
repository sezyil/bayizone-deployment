<?php

namespace App\Http\Controllers\Client;

use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Enums\Shipment\CustomerShipmentStatusEnum;
use App\Exports\ShipmentExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Shipment\ShipmentAvailableOrdersCollection;
use App\Http\Resources\Client\Shipment\ShipmentListCollection;
use App\Libraries\Client\SanctumHelper;
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
        $customer = SanctumHelper::getCustomer();
        $company_customer_id = request()->get('company_customer_id');

        $limit = DatatableResponder::getLimit();
        $current_page = DatatableResponder::getCurrentPage();

        $shipment_no = request()->get('shipment_no');
        $customer_name = request()->get('customer_name');
        $status = request()->get('status');

        //check status
        $status = $status ? (in_array($status, CustomerShipmentStatusEnum::all()) ? $status : null) : null;

        $data = $customer->shipments()
            ->when($shipment_no, function ($query) use ($shipment_no) {
                return $query->where('shipment_no', 'like', '%' . $shipment_no . '%');
            })
            ->when($customer_name, function ($query) use ($customer_name) {
                return $query->whereHas('companyCustomer', function ($query) use ($customer_name) {
                    $query->where('company_name', 'like', '%' . $customer_name . '%');
                });
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
        $customer = SanctumHelper::getCustomer();

        $validator = Validator::make($request->all(), [
            'carrier' => 'string|nullable',
            'container_no' => 'string|nullable',
            'currency' => 'required|string|in:' . implode(',', Currency::all()->pluck('code')->toArray()),
            'customer_id' => 'required|uuid',
            'note' => 'string|nullable',
            'selectedList' => 'required|array',
            'selectedList.*.order_id' => 'required|uuid',
            'selectedList.*.items' => 'required|array',
            'selectedList.*.items.*.line_id' => 'required|numeric',
            'selectedList.*.items.*.quantity' => 'required|numeric|min:1',
        ], [
            'currency.in' => 'Geçersiz para birimi.',
            'customer_id.uuid' => 'Geçersiz müşteri.',
            'selectedList.*.order_id.uuid' => 'Geçersiz sipariş.',
            'selectedList.*.items.*.line_id.numeric' => 'Geçersiz sipariş hattı.',
            'selectedList.*.items.*.quantity.numeric' => 'Geçersiz miktar.',
            'selectedList.*.items.*.quantity.min' => 'Miktar 1\'den büyük olmalıdır.',
        ]);

        if ($validator->fails()) {
            $msg = '';
            foreach ($validator->errors()->all() as $error) {
                $msg .= $error . ' ';
            }
            return Responder::send_unprocessable($msg);
        }

        $input = $validator->validated();

        //check company_customer_id
        $company_customer_id = $input['customer_id'];
        /** @var CompanyCustomer $companyCustomer */
        $companyCustomer = $customer->companyCustomers()->where('id', $company_customer_id)->first();
        if (!$companyCustomer) {
            return Responder::send_unprocessable('Müşteri bulunamadı.');
        }

        foreach ($input['selectedList'] as $order) {
            $order_id = $order['order_id'];
            $items = $order['items'];
            $order = $companyCustomer->orders()->where('id', $order_id)->first();
            if (!$order) {
                return Responder::send_unprocessable('Sipariş bulunamadı.');
            }

            foreach ($items as $item) {
                $line_id = $item['line_id'];
                $quantity = $item['quantity'];
                /** @var CustomerOrderLine $line */
                $line = $order->lines()->where('id', $line_id)->first();
                if (!$line) {
                    return Responder::send_unprocessable('Sipariş hattı bulunamadı.');
                }

                if ($line->remaining_quantity < $quantity) {
                    return Responder::send_unprocessable('Sipariş hattı için yeterli stok bulunmamaktadır.');
                }
            }
        }


        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->create([
            'company_customer_id' => $input['customer_id'],
            'company_customer_name' => $companyCustomer->company_name,
            'container_no' => $input['container_no'],
            'carrier' => $input['carrier'],
            'status' => CustomerShipmentStatusEnum::DRAFT->value,
            'note' => $input['note'],
            'currency' => $input['currency'],
        ]);

        $total_price = 0;
        $total_tax = 0;
        $total_discount = 0;
        $grand_total = 0;
        $total_weight = 0;
        $total_package = 0;
        $total_volume = 0;
        $is_international = false;
        $line_no = 1;
        foreach ($input['selectedList'] as $order) {
            $order_id = $order['order_id'];
            $items = $order['items'];
            $order = $companyCustomer->orders()->where('id', $order_id)->first();

            foreach ($items as $item) {
                $line_id = $item['line_id'];
                $quantity = $item['quantity'];
                /** @var CustomerOrderLine $line */
                $line = $order->lines()->where('id', $line_id)->first();
                $total_price += $line->unit_price * $quantity;
                $total_tax += $line->unit_price * $quantity * $line->tax_rate / 100;
                $total_discount += $line->total_discount_price;
                $grand_total += $line->unit_price * $quantity;
                $__total_weight = ($line->product?->weight ?? 0) * $quantity;
                $total_weight += $__total_weight;
                $__total_volume = $line->unit_volume * $quantity;
                $__total_package = $line->unit_package * $quantity;
                $total_package += $__total_package;
                $total_volume += $__total_volume;

                $shipment->lines()->create([
                    'customer_order_line_id' => $line_id,
                    'line_no' => 'L-' . $line_no++,
                    'quantity' => $quantity,
                    'unit_volume' => $line->unit_volume,
                    'unit_package' => $line->unit_package,
                    'weight' => $__total_weight,
                    'total_volume' => $__total_volume,
                    'total_package' => $__total_package,
                    'total_weight' => $__total_weight,
                ]);
            }
        }

        $shipment->update([
            'total_price' => $total_price,
            'total_tax' => $total_tax,
            'total_discount' => $total_discount,
            'grand_total' => $grand_total,
            'total_volume' => $total_volume,
            'total_package' => $total_package,
            'total_weight' => $total_weight,
        ]);

        $shipment->histories()->create([
            'status' => CustomerShipmentStatusEnum::DRAFT->value,
            'note' => 'Taslak oluşturuldu.',
        ]);



        return Responder::send_success(data: $shipment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
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
                    'total_volume' => $line->unit_volume * $line->quantity . ' m³',
                    'total_package' => $line->unit_package * $line->quantity,
                    'total_weight' => $line->weight * $line->quantity,
                    'product_name' => $line->orderLine->product_name,
                    'product_code' => $line->orderLine->product_code,
                    'product_image' => $line->orderLine->product_image_url,
                    'unit_price' => $shipment->getCurrency?->format($line->orderLine->unit_price, true),
                    'grand_total' => $shipment->getCurrency?->format($line->orderLine->unit_price * $line->quantity, true),
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();
        //check status only draft
        if ($shipment->status !== CustomerShipmentStatusEnum::DRAFT->value) {
            return Responder::send_not_found('Sadece taslak durumundaki sevkiyatlar düzenlenebilir.');
        }

        $shipment->load('lines');

        $tmp = [];
        foreach ($shipment->lines as $line) {
            /** @var CustomerShipmentLine $line */
            $order_id = $line?->orderLine?->customer_order->id;
            if (!isset($tmp[$order_id])) {
                $tmp[$order_id][] = [
                    'id' => $line->id,
                    'line_id' =>  $line->customer_order_line_id,
                    'quantity' => $line->quantity,
                ];
            } else {
                $tmp[$order_id][] = [
                    'id' => $line->id,
                    'line_id' =>  $line->customer_order_line_id,
                    'quantity' => $line->quantity,
                ];
            }
        }

        //key appends to selectedList
        foreach ($tmp as $key => $value) {
            $tmp[$key] = [
                'order_id' => $key,
                'items' => $value,
            ];
        }
        $tmp = array_values($tmp);

        $response = [
            'id' => $shipment->id,
            'company_customer_id' => $shipment->company_customer_id,
            'company_customer_name' => $shipment->company_customer_name,
            'container_no' => $shipment->container_no,
            'carrier' => $shipment->carrier,
            'currency' => $shipment->currency,
            'note' => $shipment->note,
            'selectedList' => $tmp,
        ];

        return Responder::send_success(data: $response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();
        //check status only draft
        if ($shipment->status !== CustomerShipmentStatusEnum::DRAFT->value) {
            return Responder::send_not_found('Sadece taslak durumundaki sevkiyatlar düzenlenebilir.');
        }

        $validator = Validator::make($request->all(), [
            'carrier' => 'string|nullable',
            'container_no' => 'string|nullable',
            'currency' => 'required|string|in:' . implode(',', Currency::all()->pluck('code')->toArray()),
            'customer_id' => 'required|uuid',
            'note' => 'string|nullable',
            'selectedList' => 'required|array',
            'selectedList.*.order_id' => 'required|uuid',
            'selectedList.*.items' => 'required|array',
            'selectedList.*.items.*.line_id' => 'required|numeric',
            'selectedList.*.items.*.quantity' => 'required|numeric|min:1',
        ], [
            'currency.in' => 'Geçersiz para birimi.',
            'customer_id.uuid' => 'Geçersiz müşteri.',
            'selectedList.*.order_id.uuid' => 'Geçersiz sipariş.',
            'selectedList.*.items.*.line_id.numeric' => 'Geçersiz sipariş hattı.',
            'selectedList.*.items.*.quantity.numeric' => 'Geçersiz miktar.',
            'selectedList.*.items.*.quantity.min' => 'Miktar 1\'den büyük olmalıdır.',
        ]);

        if ($validator->fails()) {
            $msg = '';
            foreach ($validator->errors()->all() as $error) {
                $msg .= $error . ' ';
            }
            return Responder::send_unprocessable($msg);
        }

        $input = $validator->validated();

        //check company_customer_id
        $company_customer_id = $input['customer_id'];
        /** @var CompanyCustomer $companyCustomer */
        $companyCustomer = $customer->companyCustomers()->where('id', $company_customer_id)->first();
        if (!$companyCustomer) {
            return Responder::send_unprocessable('Müşteri bulunamadı.');
        }

        foreach ($input['selectedList'] as $order) {
            $order_id = $order['order_id'];
            $items = $order['items'];
            $order = $companyCustomer->orders()->where('id', $order_id)->first();
            if (!$order) {
                return Responder::send_unprocessable('Sipariş bulunamadı.');
            }

            foreach ($items as $item) {
                $line_id = $item['line_id'];
                $quantity = $item['quantity'];
                /** @var CustomerOrderLine $line */
                $line = $order->lines()->where('id', $line_id)->first();
                if (!$line) {
                    return Responder::send_unprocessable('Sipariş hattı bulunamadı.');
                }

                if ($line->remaining_quantity < $quantity) {
                    return Responder::send_unprocessable('Sipariş hattı için yeterli stok bulunmamaktadır.');
                }
            }
        }

        $shipment->update([
            'company_customer_id' => $input['customer_id'],
            'company_customer_name' => $companyCustomer->company_name,
            'container_no' => $input['container_no'],
            'carrier' => $input['carrier'],
            'note' => $input['note'],
            'currency' => $input['currency'],
        ]);

        $total_price = 0;
        $total_tax = 0;
        $total_discount = 0;
        $grand_total = 0;
        $total_weight = 0;
        $total_package = 0;
        $total_volume = 0;
        $is_international = false;
        $line_no = 1;
        $shipment->lines()->delete();
        foreach ($input['selectedList'] as $order) {
            $order_id = $order['order_id'];
            $items = $order['items'];
            $order = $companyCustomer->orders()->where('id', $order_id)->first();

            foreach ($items as $item) {
                $line_id = $item['line_id'];
                $quantity = $item['quantity'];
                /** @var CustomerOrderLine $line */
                $line = $order->lines()->where('id', $line_id)->first();
                $total_price += $line->unit_price * $quantity;
                $total_tax += $line->unit_price * $quantity * $line->tax_rate / 100;
                $total_discount += $line->total_discount_price;
                $grand_total += $line->unit_price * $quantity;
                $__total_weight = ($line->product?->weight ?? 0) * $quantity;
                $total_weight += $__total_weight;
                $__total_volume = $line->unit_volume * $quantity;
                $__total_package = $line->unit_package * $quantity;
                $total_package += $__total_package;
                $total_volume += $__total_volume;
                $shipment->lines()->create([
                    'customer_order_line_id' => $line_id,
                    'line_no' => 'L-' . $line_no++,
                    'quantity' => $quantity,
                    'unit_volume' => $line->unit_volume,
                    'unit_package' => $line->unit_package,
                    'weight' => $__total_weight,
                    'total_volume' => $__total_volume,
                    'total_package' => $__total_package,
                    'total_weight' => $__total_weight,
                ]);
            }
        }

        $shipment->update([
            'total_price' => $total_price,
            'total_tax' => $total_tax,
            'total_discount' => $total_discount,
            'grand_total' => $grand_total,
            'total_volume' => $total_volume,
            'total_package' => $total_package,
            'total_weight' => $total_weight,
        ]);

        $shipment->histories()->create([
            'status' => CustomerShipmentStatusEnum::DRAFT->value,
            'note' => 'Taslak güncellendi.',
        ]);

        return Responder::send_success(data: $shipment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();
        //check status only draft
        $deletableStatus = [
            CustomerShipmentStatusEnum::DRAFT->value,
            CustomerShipmentStatusEnum::PENDING->value,
        ];
        if (!in_array($shipment->status, $deletableStatus)) {
            return Responder::send_unprocessable('Sadece taslak veya onay bekleyen sevkiyatlar silinebilir.');
        }
        $shipment->lines()->each(function ($line) {
            /** @var CustomerShipmentLine $line */
            $line->orderLine()->update([
                'remaining_quantity' => $line->orderLine->remaining_quantity + $line->quantity,
            ]);
        });
        $shipment->delete();

        return Responder::send_success();
    }

    public function availableOrders(Request $request): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        //currency and company_customer_id
        $company_customer_id = $request->get('company_id');
        $currency = $request->get('currency_id');

        /** @var CompanyCustomer $companyCustomer */
        $companyCustomer = $customer->companyCustomers()->where('id', $company_customer_id)->firstOrFail();
        /** @var CustomerOrder[] $orders */
        $orders = $companyCustomer->orders()->where('currency', $currency)
            /* READY_TO_SHIP or PARTIAL_SHIP group */
            ->where(function ($query) {
                $query->where('status', CustomerOrderStatusEnum::READY_TO_SHIP->value)
                    ->orWhere('status', CustomerOrderStatusEnum::PARTIALLY_SHIPPED->value);
            })
            ->managedBySystem()
            ->whereHas('lines', function ($query) {
                $query->where('remaining_quantity', '>', 0);
            })->with('lines', function ($query) {
                $query->where('remaining_quantity', '>', 0);
            })->get();
        $lines = ShipmentAvailableOrdersCollection::make($orders);

        return Responder::send_success(data: $lines);
    }

    //approve
    public function approve(string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();

        $errors = [];
        //check quantities
        foreach ($shipment->lines as $line) {
            /** @var CustomerShipmentLine $line */
            $orderLine = $line->orderLine;
            if ($orderLine->remaining_quantity < $line->quantity) {
                $errors[] = 'Sipariş No: ' . $orderLine->customer_order->order_no . ' Ürün: ' . $orderLine->product->name . ' Miktar: ' . $line->quantity . ' Adet';
            }
        }
        if (count($errors) > 0) {
            return Responder::send_unprocessable('Stok yetersiz. <br/>' . implode('<br/>', $errors));
        }

        foreach ($shipment->lines as $line) {
            /** @var CustomerShipmentLine $line */
            /** @var CustomerOrderLine $orderLine */
            $orderLine = $line->orderLine;
            $orderLine->update([
                'remaining_quantity' => $orderLine->remaining_quantity - $line->quantity,
            ]);
        }

        $shipment->update([
            'status' => CustomerShipmentStatusEnum::PENDING->value,
        ]);

        $shipment->histories()->create([
            'status' => CustomerShipmentStatusEnum::PENDING->value,
            'note' => 'Sevk Onaylandı. Gönderim Bekleniyor',
        ]);

        return Responder::send_success(data: $shipment);
    }

    //send to shipment
    public function sendShipment(string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();

        $validator = Validator::make(request()->all(), [
            'note' => 'string|nullable',
            'date' => 'date|nullable',
            'notify' => 'boolean|nullable',
        ]);


        $input = $validator->validated();

        if ($shipment->status !== CustomerShipmentStatusEnum::PENDING->value) {
            return Responder::send_unprocessable('Sevkiyat teslim edilmiş veya gönderilmiş.');
        }

        $shipment->update([
            'status' => CustomerShipmentStatusEnum::SHIPPED->value,
            //date is y-m-d
            'shipped_at' => Carbon::parse($input['date'])->isValid() ? $input['date'] : now()->format('Y-m-d'),
        ]);

        $shipment->histories()->create([
            'status' => CustomerShipmentStatusEnum::SHIPPED->value,
            'note' => $input['note'],
            'notify' => $input['notify'] ?? false,
        ]);

        $shipment->processShipmentAction();

        if ($input['notify']) {
            $companyCustomer = $shipment->companyCustomer;
            $mail = (new CompanyCustomerShipment($shipment, $input['note'], true, false))->onQueue('mail_job');
            Mail::to($companyCustomer->email)->locale($companyCustomer->language)->send($mail);
        }

        return Responder::send_success(data: $shipment);
    }

    //deliver
    public function deliverShipment(string $id): JsonResponse
    {
        $customer = SanctumHelper::getCustomer();
        /** @var CustomerShipment $shipment */
        $shipment = $customer->shipments()->where('id', $id)->firstOrFail();

        $validator = Validator::make(request()->all(), [
            'note' => 'string|nullable',
            'date' => 'date|nullable',
            'notify' => 'boolean|nullable',
        ]);
        $input = $validator->validated();

        if ($shipment->status !== CustomerShipmentStatusEnum::SHIPPED->value) {
            return Responder::send_unprocessable('Sevkiyat teslim edilmiş veya gönderilmiş.');
        }

        $shipment->update([
            'status' => CustomerShipmentStatusEnum::DELIVERED->value,
            //date is y-m-d
            'delivered_at' => Carbon::parse($input['date'])->isValid()
                ? $input['date']
                : now()->format('Y-m-d'),
        ]);

        $shipment->histories()->create([
            'status' => CustomerShipmentStatusEnum::DELIVERED->value,
            'note' => $input['note'],
            'notify' => $input['notify'] ?? false,
        ]);

        $shipment->processDeliveredAction();

        if ($input['notify']) {
            $companyCustomer = $shipment->companyCustomer;
            $mail = (new CompanyCustomerShipment($shipment, $input['note'], false, true))->onQueue('mail_job');
            Mail::to($companyCustomer->email)->locale($companyCustomer->language)->send($mail);
        }

        return Responder::send_success(data: $shipment);
    }

    public function exportToExcel(string $id): JsonResponse|BinaryFileResponse
    {
        $lang = request()->get('lang', 'tr');
        if (!in_array($lang, ['tr', 'en'])) {
            $lang = 'tr';
        }

        $customer_id = SanctumHelper::customer_id();
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
