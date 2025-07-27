<?php

namespace App\Http\Controllers\Client\Customer;

use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Exports\CustomerOrderExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CustomerOrderRequest;
use App\Http\Resources\Client\Customer\CustomerOrderDetailCollection;
use App\Http\Resources\Client\Customer\CustomerOrderListCollection;
use App\Http\Resources\Client\Customer\CustomerOrderShowCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Mail\CompanyCustomer\OrderStatusChanged;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use App\Models\Customer\CustomerOrderLineHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Mail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Maatwebsite\Excel\Facades\Excel;

class CustomerOrderController extends Controller
{
    private $permissionClass = PermissionTypes::customer_order;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //get data from query company_customer_id,customer_email,currency
        $company_customer_id = request()->get('company_customer_id') ?? null;
        $customer_email = request()->get('customer_email') ?? null;
        $currency = request()->get('currency') ?? null;
        $firstDate = request()->get('firstDate') ?? null; //'offer_create_date' | 'offer_due_date'
        $secondDate = request()->get('secondDate') ?? null;
        //check first date and second date is valid date carbon
        if ($firstDate && $secondDate) {
            try {
                $firstDate = Carbon::parse($firstDate);
                $secondDate = Carbon::parse($secondDate);
            } catch (\Exception $e) {
                return Responder::send_unprocessable('Tarihler geçersiz.');
            }
            if ($firstDate->greaterThan($secondDate)) {
                return Responder::send_unprocessable('İlk tarih ikinci tarihten büyük olamaz.');
            }
        }


        $data = CustomerOrder::where('customer_id', $customer_id)
            ->when($company_customer_id, function ($query, $company_customer_id) {
                return $query->where('company_customer_id', $company_customer_id);
            })->when($customer_email, function ($query, $customer_email) {
                return $query->whereHas('companyCustomer', function ($query) use ($customer_email) {
                    $query->where('email', 'like', '%' . $customer_email . '%');
                });
            })->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })->when($firstDate, function ($query, $firstDate) {
                return $query->where('order_date', '>=', $firstDate);
            })->when($secondDate, function ($query, $secondDate) {
                return $query->where('order_date', '<=', $secondDate);
            })
            ->orderBy('created_at', 'desc');

        $total = 0;
        $limit = DatatableResponder::getLimit(true);
        $current_page = DatatableResponder::getCurrentPage(true);
        if ($limit && $current_page) {
            $data = $data->paginate($limit, ['*'], 'page', $current_page);
            $total = $data->total();
            $data = $data->items();
        } else {
            $data = $data->get();
            $total = $data->count();
        }

        $response = new CustomerOrderListCollection($data);
        return DatatableResponder::sendResponse($response, $total);
    }

    /**
     * Display a listing of the resource. (for company)
     */
    public function list(string $company_customer): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $page = DatatableResponder::getCurrentPage();
        $limit = DatatableResponder::getLimit();
        $data = CustomerOrder::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->orderBy('created_at', 'desc')->paginate($limit, ['*'], 'page', $page);
        $result = new CustomerOrderListCollection($data->items());
        return DatatableResponder::sendResponse($result, $data->total());
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
    public function store(CustomerOrderRequest $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $input = $request->validated();
        $detail = $input['detail'];
        $detail['customer_id'] = $customer_id;
        $detail['status'] = CustomerOrderStatusEnum::DRAFT->value;
        $company_id = $detail['company_customer_id'];

        $lines = $input['lines'];

        $customerOrder = new CustomerOrder();
        $order_id = $customerOrder->saveOrder($customer_id, $detail['company_customer_id'], $detail);
        if ($order_id == null) {
            return Responder::send_unprocessable('Sipariş oluşturulamadı.');
        }


        $customerOrderLine = new CustomerOrderLine();
        $customerOrderLine->saveLines($lines, $order_id);

        return Responder::send_success($customerOrder, 'Sipariş başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer_id = SanctumHelper::customer_id();
        $order = CustomerOrder::where('id', $id)->where('customer_id', $customer_id)->firstOrFail();
        $data = new CustomerOrderShowCollection($order);

        return Responder::send_success(data: $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $id)->with('lines')->get();
        if ($customerOrder->isEmpty()) {
            return Responder::send_response(false, 'Customer order not found.',  404);
        }
        $data = new CustomerOrderDetailCollection($customerOrder);
        return Responder::send_success(data: $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerOrderRequest $request, string $company_customer_id, string $order_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //check offer id
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $order_id)->firstOrFail();
        $input = $request->validated();
        $detail = $input['detail'];
        $detail['customer_id'] = $customer_id;
        $detail['status'] = CustomerOrderStatusEnum::DRAFT->value;

        $lines = $input['lines'];

        $customerOrder = new CustomerOrder();
        $order_id = $customerOrder->saveOrder($customer_id, $detail['company_customer_id'], $detail, $order_id);
        if ($order_id == null) {
            return Responder::send_unprocessable('Customer order not created.');
        }

        $customerOrderLine = new CustomerOrderLine();
        $customerOrderLine->saveLines($lines, $order_id, true);

        return Responder::send_success($customerOrder, 'Customer order created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $company_customer, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        //get param from url customer_offer

        $customer_id = SanctumHelper::customer_id();
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->where('id', $id)->firstOrFail();
        if ($customerOrder->lines()->whereHas('shipmentLines')->exists()) {
            return Responder::send_response(false, 'Siparişe bağlı sevkiyatlar bulunmaktadır. Sipariş silinemiyor.',  422);
        }
        $offer = $customerOrder->offer()->first();
        if ($offer) {
            $offer->customer_order_id = null;
            $offer->save();
        }
        $customerOrder->delete();
        return Responder::send_success('Sipariş başarıyla silindi.');
    }

    //status update
    public function statusUpdate(Request $request, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        //validator status
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required|in:' . implode(',', CustomerOrderStatusEnum::all()),
        ]);

        $send_notification = $request->send_notify ?? false;

        $note = $request->note ?? null;

        $managed_by_system = (bool)$request->managed_by_system ?? false;

        if ($validator->fails()) {
            return Responder::send_unprocessable($validator->errors());
        }

        $customer_id = SanctumHelper::customer_id();
        $user_id = SanctumHelper::user_id();
        $user = User::find($user_id);
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $id)->first();
        if ($customerOrder == null) {
            return Responder::send_response(false, 'Müşteri Bulunamadı.',  404);
        } elseif ($customerOrder->status == $request->status) {
            return Responder::send_response(false, 'Müşteri teklif durumu aynı',  422);
        }

        if ($customerOrder->managed_by_system) {
            return Responder::send_response(false, 'Sistem tarafından yönetilen siparişlerde durum güncellenemez.',  422);
        }

        $customerOrder->status = $request->status;

        $msg = 'Müşteri teklif durumu güncellendi.';
        $customerOrder->save();


        if ($send_notification) {
            $companyCustomer = $customerOrder->companyCustomer;
            $mail = (new OrderStatusChanged($customerOrder, $request->status, $note))->onQueue('mail_job');
            Mail::to($companyCustomer->email)->locale($companyCustomer->language)->queue($mail);
            $msg = $msg . ' Bildirim gönderildi.';
        }

        $customerOrder->histories()->create([
            'note' => $note,
            'status_code' => $request->status,
            'notify' => $send_notification,
        ]);

        if ($request->status === CustomerOrderStatusEnum::APPROVED->value) {
            $customerOrder->lines->each(function ($line) {
                /** @var CustomerOrderLine $line */
                $line->status = CustomerOrderLineStatusEnum::PENDING->value;
                $line->save();

                $line->histories()->create([
                    'note' => 'Sistem: Sipariş onaylandı.',
                    'status' => CustomerOrderLineStatusEnum::PENDING->value,
                    'notify' => false,
                ]);
            });
        }

        if ($request->status === CustomerOrderStatusEnum::IN_PRODUCTION->value) {
            $customerOrder->lines->each(function ($line) {
                /** @var CustomerOrderLine $line */
                $line->status = CustomerOrderLineStatusEnum::IN_PRODUCTION->value;
                $line->save();

                $line->histories()->create([
                    'note' => 'Sistem: Üretimde.',
                    'status' => CustomerOrderLineStatusEnum::IN_PRODUCTION->value,
                    'notify' => false,
                ]);
            });
        }

        if ($request->status === CustomerOrderStatusEnum::CANCELED->value) {
            $customerOrder->lines->each(function ($line) {
                /** @var CustomerOrderLine $line */
                $line->status = CustomerOrderLineStatusEnum::CANCELED->value;
                $line->save();

                $line->histories()->create([
                    'note' => 'Sistem: Sipariş iptal edildi.',
                    'status' => CustomerOrderLineStatusEnum::CANCELED->value,
                    'notify' => false,
                ]);
            });
        }

        if ($request->status === CustomerOrderStatusEnum::REJECTED->value) {
            $customerOrder->lines->each(function ($line) {
                /** @var CustomerOrderLine $line */
                $line->status = CustomerOrderLineStatusEnum::REJECTED->value;
                $line->save();

                $line->histories()->create([
                    'note' => 'Sistem: Sipariş reddedildi.',
                    'status' => CustomerOrderLineStatusEnum::REJECTED->value,
                    'notify' => false,
                ]);
            });
        }

        if ($request->status === CustomerOrderStatusEnum::READY_TO_SHIP->value) {
            if ($managed_by_system) {
                $customerOrder->managed_by_system = true;
                $customerOrder->save();
            }
            $customerOrder->lines->each(function ($line) {
                /** @var CustomerOrderLine $line */
                $line->status = CustomerOrderLineStatusEnum::READY_TO_SHIP->value;
                $line->save();

                $line->histories()->create([
                    'note' => 'Sistem: Sevk edilmeye hazır.',
                    'status' => CustomerOrderLineStatusEnum::READY_TO_SHIP->value,
                    'notify' => false,
                ]);
            });
        }

        if ($request->status === CustomerOrderStatusEnum::DELIVERED->value) {
            $customerOrder->lines->each(function ($line) {
                /** @var CustomerOrderLine $line */
                $line->remaining_quantity = 0;
                $line->sent_quantity = $line->quantity;
                $line->save();
            });
        }

        return Responder::send_success($msg);
    }

    //history
    public function history(string $company_customer, string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('company_customer_id', $company_customer)->where('id', $id)->first();
        if ($customerOrder == null) {
            return Responder::send_response(false, 'Customer order not found.',  404);
        }
        $orderHistory = $customerOrder->histories?->each(function ($item) {
            $item->status = CustomerOrderStatusEnum::description($item->status_code);
            $item->date = $item->created_at->format('d.m.Y H:i');
        })->makeHidden(['customer_order_id', 'updated_at', 'id', 'status_code', 'created_at']);


        $lineHistory = [];
        $orderLineHistory = $customerOrder->lines?->each(function ($line) use (&$lineHistory) {
            /** @var CustomerOrderLine $line */
            $lineData = [
                'id' => $line->id,
                'product_name' => $line->product?->description->name ?? $line->product_name,
                'quantity' => $line->quantity,
                'remaining_quantity' => $line->remaining_quantity,
                'sent_quantity' => $line->sent_quantity,
                'history' => $line->histories?->makeHidden(['customer_order_line_id', 'updated_at', 'id', 'status_code'])->toArray(),
            ];
            $lineHistory[] = $lineData;
        });

        foreach ($lineHistory as $key => $line) {
            foreach ($line['history'] as $key2 => $history) {
                $lineHistory[$key]['history'][$key2]['created_at'] = Carbon::parse($history['created_at'])->format('d.m.Y H:i');
                $lineHistory[$key]['history'][$key2]['operation_date'] = Carbon::parse($history['operation_date'])->format('d.m.Y H:i');
            }
        }

        return Responder::send_success("", [
            'order' => $orderHistory,
            'lines' => $lineHistory,
        ]);
    }

    //line sent quantity update
    public function updateLineHistory(Request $request, string $order_id, int $line_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $input = $request->all();

        $customer_id = SanctumHelper::customer_id();
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $order_id)->first();
        if ($customerOrder == null) {
            return Responder::send_response(false, 'Sipariş Bulunamadı.',  404);
        }

        /** @var CustomerOrderLine $customerOrderLine */
        $customerOrderLine = $customerOrder->lines()->where('id', $line_id)->first();
        if ($customerOrderLine == null) {
            return Responder::send_response(false, 'Sipariş Detayı Bulunamadı.',  404);
        }

        $validator = Validator::make($input, [
            'note' => 'nullable|string',
            'notify' => 'boolean',
            'status' => 'string|in:' . implode(',', CustomerOrderLineStatusEnum::all()),
        ], [
            'status.in' => 'Geçersiz durum.',
            'notify.boolean' => 'Geçersiz bildirim türü.',
            'note.string' => 'Geçersiz not.',
            'status.string' => 'Geçersiz durum.',
        ]);

        if ($validator->fails()) {
            $msg = '';
            foreach ($validator->errors()->all() as $error) {
                $msg .= $error . ' ';
            }
            return Responder::send_unprocessable($msg);
        }

        $input = $validator->validated();

        $history = $customerOrderLine->histories()->create([
            'note' => $input['note'] ?? null,
            'status' => $input['status'],
            'notify' => $input['notify'],
        ]);

        $customerOrderLine->status = $input['status'];
        $customerOrderLine->save();

        return Responder::send_success('Sipariş detayı güncellendi.');
    }

    //delete line history
    public function deleteLineHistory(string $order_id, int $line_id, int $history_id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $order_id)->first();
        if ($customerOrder == null) {
            return Responder::send_response(false, 'Sipariş Bulunamadı.',  404);
        }

        /** @var CustomerOrderLine $customerOrderLine */
        $customerOrderLine = $customerOrder->lines()->where('id', $line_id)->first();
        if ($customerOrderLine == null) {
            return Responder::send_response(false, 'Sipariş Detayı Bulunamadı.',  404);
        }

        $history = $customerOrderLine->histories()->where('id', $history_id)->first();
        if ($history == null) {
            return Responder::send_response(false, 'Geçmiş Detayı Bulunamadı.',  404);
        }

        $history->delete();
        return Responder::send_success('Geçmiş detayı silindi.');
    }

    //export to excel
    public function exportToExcel(string $id): JsonResponse|BinaryFileResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);

        $lang = request()->get('lang', 'tr');
        if (!in_array($lang, ['tr', 'en'])) {
            $lang = 'tr';
        }

        $customer_id = SanctumHelper::customer_id();
        $customerOrder = CustomerOrder::where('customer_id', $customer_id)->where('id', $id)->first();
        if ($customerOrder == null) {
            return Responder::send_response(false, 'Customer order not found.',  404);
        }

        $exportClass = new CustomerOrderExport($customerOrder, $lang);
        /* return Excel::download($exportClass, 'offer.xlsx'); */
        //api response

        $fileName = $customerOrder->order_no . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download($exportClass, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
