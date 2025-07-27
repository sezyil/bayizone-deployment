<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Response\Responder;
use App\Mail\Client\OrderSuccess;
use App\Models\Order;
use App\Models\System\PaymentLog;
use App\Services\Iyzico\IyzicoPaymentChecker;
use Illuminate\Http\Request;
use Mail;

class PaymentCallbackController extends Controller
{
    public function iyzicoCallback(Request $request)
    {
        if (!$request->token) {
            return Responder::send_not_found();
        }
        $order = Order::where('payment_token', $request->token)->where('is_paid', false)->first();
        if ($order) {
            $redirectUri = env('CLIENT_APP_URL') . '/app/payments/' . $order->id;
            $success = false;
            $iyzicoPaymentChecker = new IyzicoPaymentChecker($order);
            $checkoutForm = $iyzicoPaymentChecker->checkPayment();
            if ($checkoutForm->getStatus() == 'success') {
                $redirectUri .= '/show';
                $order->approve();
                $mail = (new OrderSuccess($order))->onQueue('mail_job');
                Mail::to($order->user)->queue($mail);
                $success = true;
            } else {
                $redirectUri .= '/pay';
            }

            $paymentLog = PaymentLog::wherePaymentToken($request->token)->first();
            if ($paymentLog) {
                $paymentLog->payment_status = 'callback-' . $checkoutForm->getStatus();
                $paymentLog->save();
            }

            return redirect($redirectUri . '?success=' . $success);
        } else {
            return Responder::send_not_found();
        }
    }
}
