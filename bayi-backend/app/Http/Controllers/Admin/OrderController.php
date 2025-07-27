<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.order-index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::find($id);
        return view('admin.pages.order-show', compact('order'));
    }

    public function approve($id)
    {
        $order = Order::find($id);
        $order->waiting_transfer_approve = false;
        $order->is_paid = true;
        $order->payment_date = now();

        $order->save();

        $order->approve();

        $order->paymentLogs()->create([
            'payment_status' => 'bank_transfer_approved',
            'payment_response' => json_encode([
                'message' => 'Order approved.',
                'approved_by' => auth()->user()->fullname,
            ]),
        ]);

        return redirect()->route('admin.order.show', $id)->with('success', 'Sipariş onaylandı. TODO:Müşteriye bilgilendirme maili gönderildi.');
    }
}
