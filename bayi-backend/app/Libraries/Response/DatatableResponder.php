<?php

namespace App\Libraries\Response;

class DatatableResponder
{
    //get limit
    public static function getLimit(bool $nullable = false)
    {
        return request()->query('limit', $nullable ? null : 10);
    }

    //get current page
    public static function getCurrentPage(bool $nullable = false)
    {
        return request()->query('current_page', $nullable ? null : 1);
    }

    //get offset
    public static function getOffset()
    {
        return (self::getCurrentPage() - 1) * self::getLimit();
    }

    /**
     * Undocumented function
     *
     * @param  mixed  $data
     * @param  integer $count
     * @param  integer $http_status_code
     * @param  array   $headers
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public static function sendResponse($data, $count = 0, int $http_status_code = 200, $headers = [], $extra = [])
    {
        $response = [
            'data' => $data,
            'totalData' => $count
        ];
        if (!empty($extra)) {
            $response['extra'] = $extra;
        }

        return response()->json($response, $http_status_code, $headers);
    }
}
