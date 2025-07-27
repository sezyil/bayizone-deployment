<?php

namespace App\Http\Middleware;

use App;
use App\Libraries\Response\HttpStatusCodes;
use App\Libraries\Response\Responder;
use App\Models\Customer;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class UserStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        if (auth('sanctum')->check()) {
            $user_data = auth('sanctum')->user();
            $customerData = Customer::find($user_data->customer_id);
            if ($customerData) {
                if ($user_data->status === true && $customerData->status === true) {
                    App::setLocale($user_data->language);
                    return $next($request);
                }
            }
        }

        return response()->json(["error" => "Blocked user or organization."], HttpStatusCodes::HTTP_UNAUTHORIZED);
    }
}
