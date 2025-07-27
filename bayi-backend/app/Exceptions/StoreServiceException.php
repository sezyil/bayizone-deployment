<?php

namespace App\Exceptions;

use Exception;

class StoreServiceException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json([
            'message' => 'Store Service Exception'
        ], 500);
    }
}
