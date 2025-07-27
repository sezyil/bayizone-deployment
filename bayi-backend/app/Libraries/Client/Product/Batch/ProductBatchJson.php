<?php

namespace App\Libraries\Client\Product\Batch;

use App\Libraries\Client\FileManager\CustomerFileManager;
use App\Models\Catalog\Product\Product;
use App\Models\Customer;
use League\Csv\Reader;
use League\Csv\Writer;
use Storage;

class ProductBatchJson extends ProductBatchCore
{
    protected Customer $customer;

    protected CustomerFileManager $file_manager;

    protected $fileData;

    public function __construct($customer_id, $fileData)
    {
        parent::__construct();
        $this->customer = Customer::where('id', $customer_id)->first();
        $this->file_manager = new CustomerFileManager($customer_id);
        $this->fileData = $fileData;
    }



    public function validate()
    {
        $data = json_decode($this->fileData, true);
        $required_headers = $this->getHeaders();
        $headers = array_keys($data[0]);
        $missing_headers = array_diff($required_headers, $headers);
        if (count($missing_headers) > 0) {
            $this->addError('Missing headers: ' . implode(', ', $missing_headers));
        }
        $extra_headers = array_diff($headers, $required_headers);
        if (count($extra_headers) > 0) {
            $this->addError('Extra headers: ' . implode(', ', $extra_headers));
        }

        $hasError = count($this->getErrors()) > 0;
        if (!$hasError) {
            $this->setExtractedData($data);
            $this->validateExtractedData($this->customer->id);
        }

        return $hasError;
    }
}
