<?php

namespace App\Services\Client\Dashboard;

use App\Enums\CustomerOfferStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Enums\OfferRequestStatusEnum;
use App\Libraries\Client\SanctumHelper;
use App\Models\Catalog\Product\Product;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerTransactions\CustomerTransaction;
use App\Models\OfferRequest;
use App\Models\System\Currency;
use App\Services\Client\Dashboard\Reports\PieChartReports;
use Carbon\Carbon;

class DashboardService
{
    private $customer_id;
    private $start_date;
    private $end_date;
    public function __construct()
    {
        $this->customer_id = SanctumHelper::customer_id();
        $this->start_date = request()->get('start_date');
        $this->end_date =  request()->get('end_date');
    }

    public static function __instance()
    {
        return new self();
    }

    public static function months()
    {
        $language = 'tr';
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = 0;
        }
        return $months;
    }

    //const create 12 month with name
    public static function createDataList()
    {
        //dataset based on currency
        $dataSet = [];
        $currency = Currency::all()->pluck('code');

        foreach ($currency as $item) {
            //tailwind classes tl,usd,euro,gbp
            $backgroundColor = '';
            $borderColor = '';
            switch ($item) {
                case 'tl':
                    $backgroundColor = '#FEB2B2'; // Yumuşak kırmızı tonları
                    $borderColor = '#FED7D7'; // Aynı tonun daha açık bir tonu
                    break;
                case 'usd':
                    $backgroundColor = '#A5B4FC'; // Yumuşak mavi tonları
                    $borderColor = '#CBD5E0'; // Aynı tonun daha açık bir tonu
                    break;
                case 'euro':
                    $backgroundColor = '#9AE6B4'; // Yumuşak yeşil tonları
                    $borderColor = '#C6F6D5'; // Aynı tonun daha açık bir tonu
                    break;
                case 'gbp':
                    $backgroundColor = '#D6BCFA'; // Yumuşak mor tonları
                    $borderColor = '#E9D8FD'; // Aynı tonun daha açık bir tonu
                    break;
                default:
                    $backgroundColor = '#CBD5E0'; // Yumuşak gri tonları
                    $borderColor = '#E2E8F0'; // Aynı tonun daha açık bir tonu
                    break;
            }

            $dataSet[$item] = [
                'label' => Currency::getName($item),
                'dataset' => self::months(),
                'backgroundColor' => $backgroundColor,
                'borderColor' => $borderColor,
            ];
        }

        return $dataSet;
    }


    public static function cardsData()
    {
        $instance = self::__instance();
        $customer_id = $instance->customer_id;
        $totalOffer = CustomerOffer::where('customer_id', $customer_id)->count();
        $totalOfferRequest = OfferRequest::where('customer_id', $customer_id)->where('status', OfferRequestStatusEnum::PENDING->value)->count();
        $totalCustomer = CompanyCustomer::where('customer_id', $customer_id)->count();
        $totalProduct = Product::where('customer_id', $customer_id)->where('status', true)->count();
        $totalOrder = CustomerOrder::where('customer_id', $customer_id)->count();
        return [
            'total_offer_count' => $totalOffer,
            'total_offer_request_count' => $totalOfferRequest,
            'total_customer_count' => $totalCustomer,
            'total_product_count' => $totalProduct,
            'total_order_count' => $totalOrder
        ];
    }

    public static function pieChart()
    {
        $instance = self::__instance();
        $customer_id = $instance->customer_id;

        $data=[
            'best_selling_product' => PieChartReports::mostSellingProduct($customer_id),
            'best_selling_country' => PieChartReports::mostSellingCountry($customer_id),
            'best_selling_category' => PieChartReports::mostSellingCategory($customer_id),
        ];

        return $data;
    }

    public static function orderSummary()
    {
        $instance = self::__instance();
        $start_date = $instance->start_date;
        $end_date = $instance->end_date;
        $customer_id = $instance->customer_id;
        $model = CustomerOrder::where('customer_id', $customer_id);
        $dateColumn = 'order_date';
        if ($start_date) {
            $model->where($dateColumn, '>=', $start_date);
        }

        if ($end_date) {
            $model->where($dateColumn, '<=', $end_date);
        }

        if ($start_date && $end_date) {
            $model->whereBetween($dateColumn, [$start_date, $end_date]);
        } else if ($start_date) {
            $model->where($dateColumn, '>=', $start_date);
        } else if ($end_date) {
            $model->where($dateColumn, '<=', $end_date);
        } else {
            //current year
            $model->whereYear($dateColumn, date('Y'));
        }

        //month by month and currency
        $monthly = $model->selectRaw('MONTH(' . $dateColumn . ') as month, currency, SUM(grand_total) as total')
            ->groupBy('month', 'currency')
            ->get();

        $datalist = self::createDataList();
        foreach ($monthly as $item) {
            $month = $item->month;
            $currency = $item->currency;
            $total = $item->total;
            if (isset($datalist[$currency])) {
                $datalist[$currency]['dataset'][$month - 1] = $total;
            }
        }

        return array_values($datalist);
    }

    public static function offerSummary()
    {
        $instance = self::__instance();
        $start_date = $instance->start_date;
        $end_date = $instance->end_date;
        $customer_id = $instance->customer_id;
        $model = CustomerOffer::where('customer_id', $customer_id);
        $dateColumn = 'offer_date';
        if ($start_date) {
            $model->where($dateColumn, '>=', $start_date);
        }

        if ($end_date) {
            $model->where($dateColumn, '<=', $end_date);
        }

        if ($start_date && $end_date) {
            $model->whereBetween($dateColumn, [$start_date, $end_date]);
        } else if ($start_date) {
            $model->where($dateColumn, '>=', $start_date);
        } else if ($end_date) {
            $model->where($dateColumn, '<=', $end_date);
        } else {
            //current year
            $model->whereYear($dateColumn, date('Y'));
        }

        //month by month and currency
        $monthly = $model->selectRaw('MONTH(' . $dateColumn . ') as month, currency, SUM(grand_total) as total')
            ->groupBy('month', 'currency')
            ->get();

        $datalist = self::createDataList();

        foreach ($monthly as $item) {
            $month = $item->month;
            $currency = $item->currency;
            $total = $item->total;
            if (isset($datalist[$currency])) {
                $datalist[$currency]['dataset'][$month - 1] = $total;
            }
        }


        return array_values($datalist);
    }

    public static function transactions()
    {
        $instance = self::__instance();
        $start_date = $instance->start_date;
        $end_date = $instance->end_date;
        $customer_id = $instance->customer_id;
        $model = CustomerTransaction::where('customer_id', $customer_id);
        $dateColumn = 'date';
        if ($start_date) {
            $model->where($dateColumn, '>=', $start_date);
        }

        if ($end_date) {
            $model->where($dateColumn, '<=', $end_date);
        }

        if ($start_date && $end_date) {
            $model->whereBetween($dateColumn, [$start_date, $end_date]);
        } else if ($start_date) {
            $model->where($dateColumn, '>=', $start_date);
        } else if ($end_date) {
            $model->where($dateColumn, '<=', $end_date);
        } else {
            //current year
            $model->whereYear($dateColumn, date('Y'));
        }

        //month by month and currency is in to relationed to customer bank account
        $monthly = $model->selectRaw('MONTH(' . $dateColumn . ') as month,
                              customer_bank_accounts.currency,
                              currencies.code as currency_code,
                              SUM(amount) as total')
            ->leftJoin('customer_bank_accounts', 'customer_bank_accounts.id', '=', 'customer_bank_account_id')
            ->leftJoin('currencies', 'currencies.code', '=', 'customer_bank_accounts.currency')
            ->groupBy('month', 'customer_bank_accounts.currency')
            ->get();
        $datalist = self::createDataList();

        foreach ($monthly as $item) {
            $month = $item->month;
            $currency = $item->currency_code;
            $total = $item->total;
            if (isset($datalist[$currency])) {
                $datalist[$currency]['dataset'][$month - 1] = $total;
            }
        }

        return $monthly;
    }

    //last 5 order and offer
    public static function last5OrderAndOffer()
    {
        $instance = self::__instance();
        $customer_id = $instance->customer_id;
        $order = CustomerOrder::where('customer_id', $customer_id)->orderBy('order_date', 'desc')->limit(5)->get()->map(function ($item) {
            /** @var CustomerOrder $item */
            $tmp = [
                'id' => $item->id,
                'company' => $item->companyCustomer->company_name,
                'currency' => Currency::getName($item->currency),
                'order_date' => Carbon::parse($item->order_date)->format('d.m.Y'),
                'grand_total' => Currency::formatCurrency($item->grand_total, $item->currency, true),
                'order_no' => $item->order_no,
                'status' => CustomerOrderStatusEnum::description($item->status)
            ];
            return $tmp;
        });
        $offer = CustomerOffer::where('customer_id', $customer_id)->orderBy('offer_date', 'desc')->limit(5)->get()->map(function ($item) {
            /** @var CustomerOffer $item */
            $tmp = [
                'id' => $item->id,
                'company' => $item->company_customer->company_name,
                'currency' => Currency::getName($item->currency),
                'offer_date' => Carbon::parse($item->offer_date)->format('d.m.Y'),
                'grand_total' => Currency::formatCurrency($item->grand_total, $item->currency, true),
                'offer_no' => $item->offer_no,
                'status' => CustomerOfferStatusEnum::description($item->status)
            ];
            return $tmp;
        });
        return [
            'order' => $order,
            'offer' => $offer
        ];
    }
}
