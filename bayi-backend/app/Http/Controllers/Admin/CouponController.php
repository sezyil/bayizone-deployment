<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.coupon-index', compact('coupons'));
    }

    public function show(string $id)
    {
        $coupon = Coupon::find($id);
        $customers = Customer::all();
        return view('admin.pages.coupon-edit', compact('coupon', 'customers'));
    }

    public function create()
    {
        $coupon_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        //code exists
        while (Coupon::where('code', $coupon_code)->exists()) {
            $coupon_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        }
        $product_group = [
            Coupon::PRODUCT_GROUP_ALL => 'Tümü',
            Coupon::PRODUCT_GROUP_SUBSCRIPTION => 'Paket',
            Coupon::PRODUCT_GROUP_ADDON => 'Eklenti',
        ];
        $customers = Customer::all();

        return view('admin.pages.coupon-create', compact('coupon_code', 'product_group', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'percentage' => 'required|numeric',
            'product_group' => 'required|in:' .
                Coupon::PRODUCT_GROUP_ALL . ',' .
                Coupon::PRODUCT_GROUP_SUBSCRIPTION . ',' .
                Coupon::PRODUCT_GROUP_ADDON,
            'customer_based' => 'required|boolean',
            'customers' => 'required_if:customer_based,true|array',
            'limit' => 'nullable|integer',
            'expires_at' => 'nullable|date',
            'is_active' => 'required|boolean',
        ], []);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->percentage = $request->percentage;
        //$coupon->currency = $request->currency;
        $coupon->product_group = $request->product_group;
        $coupon->customer_based = $request->customer_based;
        $coupon->limit = $request->limit;
        $coupon->expires_at = Carbon::parse($request->expires_at)->endOfDay();
        $coupon->is_active = $request->is_active;
        $coupon->created_by = auth()->user()->id;
        $coupon->save();

        $coupon->customers()->sync($request->customer_based ? $request->customers : []);

        return redirect()->route('admin.coupon.index')->with('success', 'Kupon oluşturuldu');
    }

    public function edit(string $id)
    {
        $coupon = Coupon::findOrfail($id);
        $product_group = [
            Coupon::PRODUCT_GROUP_ALL => 'Tümü',
            Coupon::PRODUCT_GROUP_SUBSCRIPTION => 'Paket',
            Coupon::PRODUCT_GROUP_ADDON => 'Eklenti',
        ];
        $customers = Customer::all();
        return view('admin.pages.coupon-edit', compact('coupon', 'product_group', 'customers'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'percentage' => 'required|numeric',
            'product_group' => 'required|in:' .
                Coupon::PRODUCT_GROUP_ALL . ',' .
                Coupon::PRODUCT_GROUP_SUBSCRIPTION . ',' .
                Coupon::PRODUCT_GROUP_ADDON,
            'customer_based' => 'required|boolean',
            'customers' => 'required_if:customer_based,true|array',
            'limit' => 'nullable|integer',
            'expires_at' => 'nullable|date',
            'is_active' => 'required|boolean',
        ], []);

        $coupon = Coupon::findOrfail($id);
        $coupon->percentage = $request->percentage;
        //$coupon->currency = $request->currency;
        $coupon->product_group = $request->product_group;
        $coupon->customer_based = $request->customer_based;
        $coupon->limit = $request->limit;
        $coupon->expires_at = Carbon::parse($request->expires_at)->endOfDay();
        $coupon->is_active = $request->is_active;
        $coupon->updated_by = auth()->user()->id;

        $coupon->save();

        $coupon->customers()->sync($request->customer_based ? $request->customers : []);
        $coupon->syncForUpdateDetail();

        return redirect()->route('admin.coupon.index')->with('success', 'Kupon güncellendi');
    }

    public function destroy(string $id)
    {
        $coupon = Coupon::find($id);
        $coupon->deleted_by = auth()->user()->id;
        $coupon->save();
        $coupon->delete();

        return redirect()->route('admin.coupon.index')->with('success', 'Kupon silindi');
    }
}
