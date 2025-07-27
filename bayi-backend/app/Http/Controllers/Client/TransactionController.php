<?php

namespace App\Http\Controllers\Client;

use App\Enums\CustomerTransactionsFicheTypeEnum;
use App\Enums\PermissionOperationTypes;
use App\Enums\PermissionTypes;
use App\Exports\TransactionExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Transaction\TransactionShowCollection;
use App\Libraries\Client\SanctumHelper;
use App\Libraries\Permissions\ClientPermissionChecker;
use App\Libraries\Response\DatatableResponder;
use App\Libraries\Response\Responder;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\CompanyCustomer\CompanyCustomerBalance;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\System\Currency;
use App\Services\Client\Transaction\TransactionSummaryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TransactionController extends Controller
{
    private $permissionClass = PermissionTypes::transaction;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //currency filter when currency is not null.
        $currency = request()->get('currency');
        $company_customer_id = request()->get('company_customer_id');
        $email = request()->get('customer_email');
        $io_type = request()->get('io_type'); //debt,credit
        $due_status = request()->get('due_status'); //due,not_due
        $start_date = request()->get('start_date');
        $end_date = request()->get('end_date');

        $transactions = CustomerTransaction::where('customer_id', $customer_id)
            ->when($company_customer_id, function ($query, $company_customer_id) {
                return $query->where('company_customer_id', $company_customer_id);
            })
            ->when($io_type, function ($query, $io_type) {
                $type_convert = $io_type == 'debt'
                    ? CustomerTransaction::DEBT
                    : CustomerTransaction::CREDIT;
                return $query->where('io_type', $type_convert);
            })
            ->when($start_date, function ($query, $start_date) {
                return $query->where('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                return $query->where('date', '<=', $end_date);
            })
            ->when($due_status, function ($query, $due_status) {
                if ($due_status == 'due') {
                    return $query->overDue()->unpaid();
                } else {
                    return $query->notOverDue();
                }
            })
            ->when($email, function ($query, $email) {
                return $query->whereHas('companyCustomer', function ($query) use ($email) {
                    $query->where('email', 'like', '%' . $email . '%');
                });
            })
            ->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })
            ->orderBy('date', 'desc');

        if ($currency) {
            //$cloned = $transactions->clone();
            //$cloned->limit(DatatableResponder::getLimit());
            //$cloned->offset(DatatableResponder::getOffset());
            $summary = new TransactionSummaryService($transactions->clone(), $currency);
        }

        $transactions = $transactions->paginate(DatatableResponder::getLimit(), ['*'], 'page', DatatableResponder::getCurrentPage());


        $total = $transactions->total();
        $items = $transactions->transform(function ($transaction) {
            /** @var CustomerTransaction $transaction */
            return [
                'id' => $transaction->id,
                'fiche_no' => $transaction->fiche_no,
                'company_customer_id' => $transaction->company_customer_id,
                'company_customer' => $transaction->companyCustomer->company_name,
                'fiche_type' => CustomerTransactionsFicheTypeEnum::description($transaction->fiche_type),
                'date' => $transaction->date,
                'formatted_date' => Carbon::parse($transaction->date)->format('d.m.Y'),
                'customer_order_id' => $transaction->order?->id ?? null,
                'order_number' => $transaction->order?->order_no ?? null,
                'due_date' => $transaction->due_date,
                'formatted_due_date' => $transaction->due_date ? Carbon::parse($transaction->due_date)->format('d.m.Y') : null,
                'amount' => $transaction->amount,
                'formatted_amount' => $transaction?->getCurrency?->format($transaction->amount, true) ?? 0,
                'io_type' => (int)$transaction->io_type,
                'is_paid' => $transaction->is_paid,
                'description' => $transaction->description,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ];
        });
        if ($currency) {
            return DatatableResponder::sendResponse($items, $total, extra: $summary->getSummary());
        } else {
            return DatatableResponder::sendResponse($items, $total);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $currency = $request->get('currency');
        $company_customer_id = $request->get('company_customer_id');
        $currencies = Currency::all()->pluck('code');
        if (!$company_customer_id) return Responder::send_unprocessable("Müşteri seçimi zorunludur.");
        if (!$currency) return Responder::send_unprocessable("Para birimi seçimi zorunludur.");
        if (!$currencies->contains($currency)) return Responder::send_unprocessable("Para birimi bulunamadı.");


        /** @var CompanyCustomer $customerCompanies */
        $customerCompanies = $customer->companyCustomers()->where('id', $company_customer_id)->first();
        if (!$customerCompanies) {
            return Responder::send_unprocessable("Müşteri bulunamadı.");
        }

        $customerOrders = $customerCompanies->orders()
            ->doesntHave('transaction')
            ->notDraft()
            ->whereCurrency($currency)
            ->get();

        $customerOrders = $customerOrders->transform(function ($order) {
            /** @var CustomerOrder $order */
            return [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'grand_total' => $order->grand_total,
                'formatted_grand_total' => $order->getCurrency->format($order->grand_total, true),
            ];
        });

        return Responder::send_success(data: [
            'customer_orders' => $customerOrders
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::CREATE, $this->permissionClass);
        $customer = SanctumHelper::getCustomer();
        $customerOrders = $customer->customerOrders()->notDraft()->whereDoesntHave('transaction')->get()->pluck('id');
        $companyCustomers = $customer->companyCustomers()->pluck('id');
        $customer_id = $customer->id;
        $validator = Validator::make($request->all(), [
            "company_customer_id" => [
                "required",
                Rule::in($companyCustomers)
            ],
            "currency" => [
                "required",
                Rule::exists('currencies', 'code')
            ],
            "date" => "required|date",
            "description" => "string|nullable",
            "due_date" => "nullable|date",
            "fiche_type" => [
                "required",
                Rule::in(CustomerTransactionsFicheTypeEnum::all(), 'value')
            ],
            'amount' => 'required|numeric|min:1',
            "is_paid" => "boolean",
        ], [
            "company_customer_id.required" => "Müşteri seçimi zorunludur.",
            "company_customer_id.exists" => "Müşteri bulunamadı.",
            "currency.required" => "Para birimi seçimi zorunludur.",
            "currency.exists" => "Para birimi bulunamadı.",
            "date.required" => "Tarih seçimi zorunludur.",
            "date.date" => "Tarih formatı hatalı.",
            "description.required" => "Açıklama zorunludur.",
            "description.string" => "Açıklama metin tipinde olmalıdır.",
            "due_date.date" => "Vade tarihi formatı hatalı.",
            "fiche_no.required" => "Fiş numarası zorunludur.",
            "fiche_no.string" => "Fiş numarası metin tipinde olmalıdır.",
            "fiche_type.required" => "Fiş tipi seçimi zorunludur.",
            "fiche_type.in" => "Fiş tipi hatalı.",
            "amount.required" => "Tutar zorunludur.",
            "amount.numeric" => "Tutar sayısal olmalıdır.",
            "amount.min" => "Tutar en az 1 olmalıdır.",
            "is_paid.boolean" => "Ödeme durumu hatalı.",
        ]);

        if ($validator->fails()) {
            return Responder::send_unprocessable("", $validator->errors());
        }

        $transaction = new CustomerTransaction();
        $transaction->customer_id = $customer_id;
        $transaction->company_customer_id = $request->company_customer_id;
        $transaction->date = $request->date;
        $transaction->description = $request->description;
        $transaction->fiche_type = $request->fiche_type;
        if ($transaction->fiche_type == CustomerTransactionsFicheTypeEnum::RECEIPT->value) {
            $transaction->is_paid = true;
        } else {
            $transaction->is_paid = $request->is_paid;
        }



        $transaction->due_date = $request->due_date;
        if ($request->order_based) {
            $transaction->customer_order_id = $request->customer_order_id;
            $transaction->assignOrderTotals();
        } else {
            $transaction->currency = $request->currency;
            $transaction->amount = $request->amount;
        }

        if (in_array($transaction->fiche_type, CustomerTransactionsFicheTypeEnum::debtGroup())) {
            $transaction->io_type = CustomerTransaction::DEBT;
        } else if (in_array($transaction->fiche_type, CustomerTransactionsFicheTypeEnum::creditGroup())) {
            $transaction->io_type = CustomerTransaction::CREDIT;
        } else {
            return Responder::send_unprocessable("Fiş tipi hatalı.");
        }

        $transaction->customer_order_id = $request->customer_order_id ?? null;
        $transaction->amount = $request->amount;


        if ($transaction->is_paid) {
            $transaction->payment_closed_date = Carbon::now();
        }


        $transaction->currency = $request->currency;


        $transaction->save();

        $balances = $transaction->companyCustomer->balances()->isCurrency($transaction->currency)->first();
        $balances->syncAll();


        return Responder::send_success("İşlem başarıyla kaydedildi.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $transaction = CustomerTransaction::where('customer_id', $customer_id)->where('id', $id)->firstOrFail();
        /** @var CustomerTransaction $transaction */

        $response = new TransactionShowCollection($transaction);


        return Responder::send_success(data: $response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::DELETE, $this->permissionClass);
        $transaction = CustomerTransaction::where('customer_id', SanctumHelper::customer_id())->where('id', $id)->firstOrFail();
        $companyCustomer = $transaction->companyCustomer;
        $transaction->delete();
        $companyCustomer->balances()->isCurrency($transaction->currency)->first()->syncAll();

        return Responder::send_success("İşlem başarıyla silindi.");
        //
    }

    //pay
    public function markAsPaid(string $id): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::UPDATE, $this->permissionClass);
        $transaction = CustomerTransaction::where('customer_id', SanctumHelper::customer_id())->where('id', $id)->unpaid()->firstOrFail();
        $transaction->is_paid = true;
        $transaction->payment_closed_date = Carbon::now();
        $transaction->save();
        $companyCustomer = $transaction->companyCustomer;
        $companyCustomer->balances()->isCurrency($transaction->currency)->first()->syncAll();
        return Responder::send_success("İşlem başarıyla ödendi.");
    }

    //balance
    public function balance(): JsonResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        $company_customer_id = request()->get('company_customer_id');
        $currency = request()->get('currency');
        $companyCustomer = CompanyCustomer::where('customer_id', $customer_id)->where('id', $company_customer_id)->firstOrFail();
        /** @var CompanyCustomerBalance $balances */
        $balances = $companyCustomer->balances()->isCurrency($currency)->firstOrFail();
        $response = [
            'balance' => $balances->balance,
            'formatted_balance' => $balances->getCurrency->format($balances->balance, true),
            'total_debt' => $balances->total_debt,
            'formatted_total_debt' => $balances->getCurrency->format($balances->total_debt, true),
            'total_credit' => $balances->total_credit,
            'formatted_total_credit' => $balances->getCurrency->format($balances->total_credit, true),
            'total_paid_debt' => $balances->total_paid_debt,
            'formatted_total_paid_debt' => $balances->getCurrency->format($balances->total_paid_debt, true),
            'total_paid_credit' => $balances->total_paid_credit,
            'formatted_total_paid_credit' => $balances->getCurrency->format($balances->total_paid_credit, true),
            'total_unpaid_debt' => $balances->total_unpaid_debt,
            'formatted_total_unpaid_debt' => $balances->getCurrency->format($balances->total_unpaid_debt, true),
            'total_unpaid_credit' => $balances->total_unpaid_credit,
            'formatted_total_unpaid_credit' => $balances->getCurrency->format($balances->total_unpaid_credit, true),
            'total_overdue_debt' => $balances->total_overdue_debt,
            'formatted_total_overdue_debt' => $balances->getCurrency->format($balances->total_overdue_debt, true),
            'total_overdue_credit' => $balances->total_overdue_credit,
            'formatted_total_overdue_credit' => $balances->getCurrency->format($balances->total_overdue_credit, true),
        ];
        return Responder::send_success("", $response);
    }


    //excel download
    public function exportToExcel(): JsonResponse|BinaryFileResponse
    {
        ClientPermissionChecker::check(PermissionOperationTypes::VIEW, $this->permissionClass);
        $customer_id = SanctumHelper::customer_id();
        //currency filter when currency is not null.
        $currency = request()->get('currency');
        $company_customer_id = request()->get('company_customer_id');
        $email = request()->get('customer_email');
        $io_type = request()->get('io_type'); //debt,credit
        $due_status = request()->get('due_status'); //due,not_due
        $start_date = request()->get('start_date');
        $end_date = request()->get('end_date');

        if (!$currency || Currency::where('code', $currency)->doesntExist()) {
            return Responder::send_unprocessable("Para birimi bulunamadı.");
        }

        $transactions = CustomerTransaction::where('customer_id', $customer_id)
            ->when($company_customer_id, function ($query, $company_customer_id) {
                return $query->where('company_customer_id', $company_customer_id);
            })
            ->when($io_type, function ($query, $io_type) {
                $type_convert = $io_type == 'debt'
                    ? CustomerTransaction::DEBT
                    : CustomerTransaction::CREDIT;
                return $query->where('io_type', $type_convert);
            })
            ->when($start_date, function ($query, $start_date) {
                return $query->where('date', '>=', $start_date);
            })
            ->when($end_date, function ($query, $end_date) {
                return $query->where('date', '<=', $end_date);
            })
            ->when($due_status, function ($query, $due_status) {
                if ($due_status == 'due') {
                    return $query->overDue()->unpaid();
                } else {
                    return $query->notOverDue();
                }
            })
            ->when($email, function ($query, $email) {
                return $query->whereHas('companyCustomer', function ($query) use ($email) {
                    $query->where('email', 'like', '%' . $email . '%');
                });
            })
            ->when($currency, function ($query, $currency) {
                return $query->where('currency', $currency);
            })
            ->orderBy('date', 'desc');

        if ($transactions->count() == 0) {
            return Responder::send_unprocessable("Veri bulunamadı.");
        }


        $summary = new TransactionSummaryService($transactions->clone(), $currency);

        $exportClass = new TransactionExport($transactions->get(), $summary->getSummary(), $start_date, $end_date);

        $fileName = 'transactions-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download($exportClass, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
