<?php

namespace App\Http\Controllers\Client\Product;

use App\Http\Controllers\Controller;
use App\Libraries\Client\FileManager\CustomerFileManager;
use App\Libraries\Response\Responder;
use App\Libraries\Client\SanctumHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class ProductImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            /* 10mb */
            'file' => 'file|required|mimes:jpg,jpeg,png|max:10240',
        ], [
            'file.required' => 'Dosya yüklemesi zorunludur.',
            'file.mimes' => 'Dosya formatı jpg,jpeg,png olmalıdır.',
            'file.max' => 'Dosya boyutu en fazla 10mb olabilir.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return Responder::send_unprocessable("", $errors);
        } else {

            $customer_id = SanctumHelper::customer_id();

            $file_path = CustomerFileManager::uploadToCacheFolder($customer_id, $request->file('file'));
            return Responder::send_success("Success!", [
                'image_name' => $file_path,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        //
    }
}
