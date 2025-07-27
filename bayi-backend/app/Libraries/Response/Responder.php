<?php

namespace App\Libraries\Response;

class Responder
{
    public static function send_success(string $msg = '', mixed $data = [])
    {
        return self::send_response(true, $msg, HttpStatusCodes::HTTP_OK, $data, []);
    }

    public static function send_bad_request(string $msg, $errors = [])
    {
        return self::send_response(false, $msg, HttpStatusCodes::HTTP_BAD_REQUEST, null, $errors);
    }

    public static function send_unprocessable(string $msg = "", $errors = [])
    {
        return self::send_response(false, $msg, HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY, null, $errors);
    }

    public static function send_unauthorized(string $msg = null)
    {
        if (!$msg) {
            $msg = "Unauthorized Access!";
        }
        return self::send_response(false, $msg, HttpStatusCodes::HTTP_UNAUTHORIZED, null, []);
    }

    //forbidden
    public static function send_forbidden($msg = null)
    {
        if (!$msg) {
            $msg = "Forbidden Access!";
        }
        return self::send_response(false, $msg, HttpStatusCodes::HTTP_FORBIDDEN, null, []);
    }

    public static function send_not_found(string $msg = null)
    {
        if (!$msg) {
            $msg = "Not Found!";
        }
        return self::send_response(false, $msg, HttpStatusCodes::HTTP_NOT_FOUND, null, []);
    }

    public static function send_response(bool $success, string $msg = "", int $http_code, $data = [], $errors = [])
    {
        $response = [
            'success' => $success,
        ];

        if ($msg != "") $response['msg'] = $msg;
        if ($data !== null) $response['data'] = $data;
        if ($errors) $response['errors'] = $errors;

        return response()->json($response, $http_code);
    }
}
