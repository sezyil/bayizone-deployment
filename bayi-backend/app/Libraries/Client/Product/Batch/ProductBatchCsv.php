<?php

namespace App\Libraries\Client\Product\Batch;

use App\Libraries\Client\FileManager\CustomerFileManager;
use App\Models\Catalog\Product\Product;
use App\Models\Customer;
use League\Csv\Reader;
use League\Csv\Writer;
use Storage;

class ProductBatchCsv extends ProductBatchCore
{
    protected Customer $customer;

    protected CustomerFileManager $file_manager;

    protected $fileData;
    public function __construct($customer_id = null, $fileData = null)
    {
        parent::__construct();
        if ($customer_id) {
            $this->customer = Customer::where('id', $customer_id)->first();
            $this->file_manager = new CustomerFileManager($customer_id);
        }
        if ($fileData) {
            $this->file = $fileData;
        }

    }

    public function validate()
    {
        $csv = Reader::createFromString($this->fileData);
        $csv->setDelimiter($this->arrayDelimiter);
        $csv->setHeaderOffset(0);
        $required_headers = $this->getHeaders();
        $headers = $csv->getHeader();
        $existData = $csv->getRecords();
        $existData = iterator_to_array($existData, false);
        $dataCount = count($existData);
        $missing_headers = array_diff($required_headers, $headers);
        if (count($missing_headers) > 0) {
            $this->addError('Hatalı Başlık: ' . implode(', ', $missing_headers));
        }
        $extra_headers = array_diff($headers, $required_headers);
        if (count($extra_headers) > 0) {
            $this->addError('Gereksiz Başlıklar: ' . implode(', ', $extra_headers));
        }

        $hasError = count($this->getErrors()) > 0 || $dataCount == 0;


        if (!$hasError && $dataCount > 0) {
            $existData = array_map(function ($item) {
                $item['images'] = explode($this->imageDelimiter, $item['images']);
                return $item;
            }, $existData);
            $this->setExtractedData($existData);
            $this->validateExtractedData($this->customer->id);
        } else {
            $this->addError('No data found');
        }

        return $hasError;
    }
}
