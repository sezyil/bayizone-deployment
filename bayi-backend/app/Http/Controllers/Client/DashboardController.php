<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Libraries\Response\Responder;

use App\Services\Client\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function cardData(): JsonResponse
    {
        return Responder::send_success("", DashboardService::cardsData());
    }

    public function orderSummary(): JsonResponse
    {
        return Responder::send_success("", DashboardService::orderSummary());
    }

    public function offerSummary(): JsonResponse
    {
        return Responder::send_success("", DashboardService::offerSummary());
    }

    //lastFiveOrdersOffers
    public function lastFiveOrdersOffers(): JsonResponse
    {
        return Responder::send_success("", DashboardService::last5OrderAndOffer());
    }

    //lastFiveTransactions
    public function lastFiveTransactions(): JsonResponse
    {
        return Responder::send_success("", DashboardService::transactions());
    }

    //pieChart
    public function pieChart(): JsonResponse
    {
        return Responder::send_success("", DashboardService::pieChart());
    }
}
