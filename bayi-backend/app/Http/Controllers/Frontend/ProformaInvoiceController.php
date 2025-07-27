<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CustomerOfferStatusEnum;
use App\Http\Controllers\Controller;
use App\Mail\Client\OfferStatusChanged;
use App\Models\CompanyCustomer\CompanyCustomer;
use App\Models\Customer\CustomerOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Mail;

class ProformaInvoiceController extends Controller
{

    //get proforma id for panel user
    public function index(string $proformaId, $userId)
    {
        if (!$proformaId) {
            return abort(404, 'Proforma id not found');
        }
        //check password param query string
        if (!$userId) {
            return abort(404, 'Token not found');
        }

        //check lang query string
        $lang = request()->query('lang');
        if ($lang && in_array($lang, ['tr', 'en'])) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('tr');
        }
        //check token is valid sanctum token

        /* $table = Sanctum::personalAccessTokenModel();
        $token = $table::findToken($token);
        if (!$token) {
            return abort(404);
        } */

        //check proforma id
        $proforma = CustomerOffer::where('id', $proformaId)->with('lines')->first();
        if (!$proforma) {
            return abort(401, 'Proforma not found');
        } else {
            //check user id
            $user = $proforma->customer()->first()->users()->where('id', $userId)->first();
            if (!$user) {
                return abort(401, 'Token not found');
            }
        }

        $params = [
            'data' => $proforma,
            'type' => 'preview',
            'user_id' => $userId,
            'logo' => $proforma->customer->image ?? 'uploads/client/no_image.png',
        ];

        return view('proforma', $params);
    }

    //proforma function for company customer
    public function forCustomer(string $proformaId)
    {
        if (!$proformaId) {
            return abort(404, 'Proforma id not found');
        }
        $password = request()->query('pass');
        if (!$password) {
            $uri = route('proforma-invoice.password', ['proformaId' => $proformaId]);
            return view('proformapassword', [
                'url' => $uri,
            ]);
        }

        //check lang query string
        $lang = request()->query('lang');
        if ($lang && in_array($lang, ['tr', 'en'])) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('tr');
        }
        //check token is valid sanctum token

        /* $table = Sanctum::personalAccessTokenModel();
        $token = $table::findToken($token);
        if (!$token) {
            return abort(404);
        } */

        //check proforma id
        $proforma = CustomerOffer::where('id', $proformaId)->where('password', $password)->with('lines')->first();
        if (!$proforma) {
            return abort(401, 'Proforma not found');
        }

        $due_date = Carbon::parse($proforma->offer_due_date)->format('Y-m-d');
        if ($due_date < now()->format('Y-m-d')) {
            echo "<script>alert('Proforma süresi dolmuştur.')</script>";
            return abort(404, __('offer-status.EXPIRED'));
        }



        return view('proforma', [
            'data' => $proforma,
            'type' => 'approval',
            'changeable' => $proforma->status == CustomerOfferStatusEnum::PENDING->value,
            'password' => $password,
            'logo' => $proforma->customer->image ?? 'uploads/client/no_image.png',
        ]);
    }

    //proforma approve or reject
    public function approval(Request $request, string $proformaId)
    {
        //csrf token check
        if (!csrf_token()) {
            return abort(401, 'Unauthorized');
        }

        $response = [
            'status' => false,
            'message' => 'Unprocessable Entity',
            'errors' => []
        ];
        if (!$proformaId) {
            $response['errors'][] = 'Proforma id not found';
        }
        $password = $request->input('password');
        if (!$password) {
            $response['errors'][] = 'Password not found';
        }
        $type = $request->input('type');
        if (!$type) {
            $response['errors'][] = 'Type not found';
        } else {
            if ($type != 'approve' && $type != 'reject') {
                $response['errors'][] = 'Type not found';
            }
        }
        $user = null;
        //check proforma id
        $proforma = CustomerOffer::where('id', $proformaId)->where('password', $password)->first();
        if (!$proforma) {
            $response['errors'][] = 'Proforma not found';
        } else {
            $due_date = $proforma->offer_due_date;
            if ($due_date < now()) {
                $response['errors'][] = 'Proforma expired';
            }
            $inputMail = $request->input('mail');
            if ($inputMail) {
                $customer_id = $proforma->customer_id;
                //search mail in company customer
                $user = $proforma->customer->companyCustomers()->where('email', $inputMail)->first();
                if (!$user) {
                    $response['errors'][] = 'Mail not found';
                }
            } else {
                $response['errors'][] = 'Mail not found';
            }
        }

        if (count($response['errors']) > 0) {
            return response()->json($response, 422);
        }

        /** @var CompanyCustomer $user */
        $authorizedFullname = $user->authorized_name;
        //check approval or reject
        if ($type == 'approve' && $proforma->status == CustomerOfferStatusEnum::PENDING->value) {
            $proforma->status = CustomerOfferStatusEnum::APPROVED->value;
            $proforma->approve_date = now();
            $proforma->approved_user_name = $authorizedFullname;
            $proforma->save();
            $response['status'] = true;
            $response['message'] = 'Proforma approved';
        } elseif ($type == 'reject' && $proforma->status == CustomerOfferStatusEnum::PENDING->value) {
            $proforma->status = CustomerOfferStatusEnum::REJECTED->value;
            $proforma->reject_date = now();
            $proforma->rejected_user_name = $authorizedFullname;
            $proforma->save();
            $response['status'] = true;
            $response['message'] = 'Proforma rejected';
        } else {
            $response['errors'][] = 'An error occurred while processing your request for approval or rejection.';
        }

        if ($response['status']) {
            //send mail
            Mail::to($user->customer->email)->send(new OfferStatusChanged($proforma, $proforma->status));
        }

        return response()->json($response, 200);
    }
}
