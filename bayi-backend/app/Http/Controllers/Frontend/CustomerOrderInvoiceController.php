<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer\CustomerOrder;

class CustomerOrderInvoiceController extends Controller
{

    //get proforma id for panel user
    public function index(string $orderId)
    {
        $password = request()->query('pass');
        if (!$orderId) {
            return abort(404, 'Order Not Found');
        }

        if (!$password) {
            //access denied
            return abort(401, 'Access Denied');
        }

        //check lang query string
        $lang = request()->query('lang');
        if ($lang && in_array($lang, ['tr', 'en'])) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('tr');
        }

        //check proforma id
        $proforma = CustomerOrder::where('id', $orderId)->where('password', $password)->with('lines')->first();
        if (!$proforma) {
            return abort(401, 'Proforma not found');
        }

        $params = [
            'data' => $proforma,
            'type' => 'preview',
            'logo' => $proforma->customer->image ?? 'uploads/client/no_image.png',
        ];

        return view('customerorder', $params);
    }
}
