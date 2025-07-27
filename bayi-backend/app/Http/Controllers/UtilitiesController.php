<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Client\Product\Batch\ProductBatchCore;
use App\Libraries\Client\Product\Batch\ProductBatchCsv;
use App\Libraries\Response\Responder;
use App\Models\System\Countries;
use App\Models\System\Currency;
use App\Models\System\States;
use App\Models\System\Unit;
use DB;
use Illuminate\Http\Request;

class UtilitiesController extends Controller
{
    /* countries */
    public function Countries()
    {
        $result = Countries::all();
        $data = [];
        foreach ($result as $key => $value) {
            $translations = json_decode($value->translations);

            //if has tr and tr is not empty
            if (isset($translations->tr) && !empty($translations->tr)) {
                $value->name = $translations->tr;
            }

            $data[] = [
                'id' => $value->id,
                'name' => $value->name,
            ];
        }
        return Responder::send_success("", $data);
    }

    /* States */
    public function States(int $country_id)
    {
        $result = Countries::find($country_id);
        if (!$result)
            return Responder::send_bad_request("Country not found");
        //id name sort order name
        $data = $result->states()->select('id', 'name')->orderBy('name')->get();
        return Responder::send_success("", $data);
    }

    /* Cities */
    public function Cities($state_id)
    {
        $result = States::find($state_id);
        if (!$result)
            return Responder::send_bad_request("State not found");

        $data = $result->cities()->select('id', 'name')->orderBy('name')->get();

        return Responder::send_success("", $data);
    }


    //Currency list
    public function Currencies()
    {
        return Responder::send_success("", Currency::all());
    }

    //units
    public function Units()
    {
        $units = Unit::all();
        return Responder::send_success("", $units);
    }

    //batch csv sample
    public function batchCsvSample()
    {
        $file_path = ProductBatchCore::createTemplate('csv');
        return response()->download($file_path);
    }

    //batch json sample
    public function batchJsonSample()
    {
        $file_path = ProductBatchCore::createTemplate('json');
        return response()->download($file_path);
    }

    //client privacy policy
    public function clientPrivacyPolicy()
    {
        $viewString = view('shared.client-privacy-policy')->render();
        return Responder::send_success(data: $viewString);
    }
}
