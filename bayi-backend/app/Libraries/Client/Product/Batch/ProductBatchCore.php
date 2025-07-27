<?php

namespace App\Libraries\Client\Product\Batch;

use App\Enums\BatchProcessesTypesEnum;
use App\Jobs\Client\BatchProductJob;
use App\Libraries\Client\FileManager\CustomerFileManager;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\Customer;
use App\Models\System\BatchProcesses;
use League\Csv\Reader;
use League\Csv\Writer;
use Storage;
use Str;
use Symfony\Component\Uid\Ulid;

class ProductBatchCore
{
    const VERSION = '01';
    protected $headers = [];
    protected $arrayDelimiter = ',';
    protected $imageDelimiter = ';';

    public \Illuminate\Http\UploadedFile $file;

    private array $extractedData = [];

    public function __construct()
    {
        $this->setHeaders();
    }

    private $errors = [];

    private function setHeaders()
    {
        $this->headers = [
            'name',
            'model',
            'sku',
            'price',
            'quantity',
            'description',
            'images',
        ];
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getSample($type = 'csv')
    {
        $sample = [];
        foreach ($this->headers as $key => $value) {
            $sample[$value] = 'demo';
            if ($value == 'images') {
                $images = [
                    "https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png",
                    "https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png"
                ];
                if ($type == 'csv') {
                    $sample[$value] = implode($this->arrayDelimiter, $images);
                } else {
                    $sample[$value] = $images;
                }
            }
        }
        if ($type == 'json') {
            $sample = [$sample];
        }
        return $sample;
    }

    public function getArrayDelimiter()
    {
        return $this->arrayDelimiter;
    }

    /**
     * Create template csv or json file
     *
     * @param string $fileType csv or json
     * @return string $file_path
     */
    public static function createTemplate($fileType)
    {
        $instance = new self();
        $templatePath = storage_path('app/public/uploads/system');
        $file_path = $templatePath . '/product_template' . self::VERSION . '.' . $fileType;
        if (!file_exists($templatePath)) {
            mkdir($templatePath, 0777, true);
        }
        if (!file_exists($file_path)) {
            if ($fileType == 'csv') {
                $csv = Writer::createFromPath($file_path, 'w+');
                $csv->setDelimiter($instance->arrayDelimiter);
                //insert headers
                $csv->insertOne($instance->getHeaders());

                //insert sample data
                $csv->insertOne($instance->getSample('csv'));
            } else {
                $json = json_encode($instance->getSample('json'));
                file_put_contents($file_path, $json);
            }
        }


        return $file_path;
    }


    //set extracted data
    public function setExtractedData($data)
    {
        $this->extractedData = $data;
    }

    //get extracted data
    public function getExtractedData()
    {
        return $this->extractedData;
    }

    //add error
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    //get errors
    public function getErrors()
    {
        return $this->errors;
    }

    //check image urls
    public function checkImageUrls($images)
    {
        $errors = [];
        foreach ($images as $key => $value) {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[] = 'Resim url geçersiz: ' . $value;
            } else {
                $headers = get_headers($value);
                if (strpos($headers[0], '200') === false) {
                    $errors[] = 'Resim url geçersiz: ' . $value;
                } else {
                    //check if image is valid
                    $image = file_get_contents($value);
                    $f = finfo_open();
                    $mime_type = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
                    if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/jpg'])) {
                        $errors[] = 'Resim uzantısı geçersiz: ' . $value;
                    }
                }
            }
        }
        return $errors;
    }

    //validate extracted data
    public function validateExtractedData($customer_id)
    {
        $errors = [];
        $i = 0;
        $existProducts = Product::where('customer_id', $customer_id)->get();
        $existProductIds = $existProducts->pluck('id')->toArray();
        $existModels = $existProducts->pluck('model')->toArray();
        $existSkus = $existProducts->pluck('sku')->toArray();
        foreach ($this->extractedData as $key => $value) {
            $value = array_map(function ($item) {
                if (!is_array($item)) {
                    return trim($item);
                }
                return $item;
            }, $value);
            if (!isset($value['name'])) $errors[$i][] = 'İsim gerekli';
            else {
                if (strlen($value['name']) < Product::NAME_MIN_LENGTH) $errors[$i][] = 'İsim en az 6 karakter olmalı';
                if (strlen($value['name']) > Product::NAME_MAX_LENGTH) $errors[$i][] = 'İsim en fazla 120 karakter olmalı';
                if (ProductDescription::whereIn('product_id', $existProductIds)->where('name', $value['name'])->exists()) $errors[$i][] = 'Bu isim zaten kullanılıyor';
            }
            if (!isset($value['model'])) $errors[$i][] = 'Model gerekli';
            else {
                if (strlen($value['model']) < Product::MODEL_MIN_LENGTH) $errors[$i][] = 'Model en az 6 karakter olmalı';
                elseif (strlen($value['model']) > Product::MODEL_MAX_LENGTH) $errors[$i][] = 'Model en fazla 20 karakter olmalı';
                elseif (in_array($value['model'], $existModels)) $errors[$i][] = 'Bu model kodu zaten kullanılıyor';
            }
            if (!isset($value['sku'])) $errors[$i][] = 'Sku gerekli';
            else {
                //check for
                if (in_array($value['sku'], $existSkus)) $errors[$i][] = 'Bu sku zaten kullanılıyor';
            }
            if (!isset($value['price']) || !is_numeric($value['price'])) $errors[$i][] = 'Fiyat gerekli/Geçersiz';
            if (!isset($value['quantity']) || !is_numeric($value['quantity'])) $errors[$i][] = 'Miktar gerekli/Geçersiz';
            if (!isset($value['description'])) $errors[$i][] = 'Açıklama gerekli';
            if (!isset($value['images'])) $errors[$i][] = 'Resim gerekli';
            if (!isset($value['minimum']) || !is_numeric($value['minimum'])) $errors[$i][] = 'Minimum miktar gerekli/Geçersiz';
            else {
                if ($value['minimum'] < 0) $errors[$i][] = 'Minimum miktar en az 0 olmalı';
            }



            $i++;
        }
        //push errors
        foreach ($errors as $key => $value) {
            $this->addError('Satır ' . ($key + 1) . ': ' . implode(', ', $value));
        }
        return $errors;
    }

    /**
     * Extract data from file
     *
     * @param string $customer_id
     * @param \Illuminate\Http\UploadedFile $file
     * @return string $file_path
     */
    public function upload($customer_id, \Illuminate\Http\UploadedFile $file)
    {
        $this->file = $file;
        $fileManager = new CustomerFileManager($customer_id);
        //generate random file name
        $file_name = 'product_' . Str::random(8) . time() . '.json';
        //new file
        $folder_path = $fileManager->getBatchFolder();
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }
        $newFilePath = $folder_path . '/' . $file_name;
        $this->file->move($folder_path, $file_name);
        return $newFilePath;
    }

    //add to queue
    public function addQueue($customer_id, string $file_path, string $file_type)
    {
        $data = [
            'customer_id' => $customer_id,
            'payload' => json_encode([
                'file_path' => $file_path,
                'file_type' => $file_type,
                'count' => null
            ]),
            'type' => BatchProcessesTypesEnum::PRODUCT->value,
            'is_completed' => false,
            'is_failed' => false,
            'is_system' => false
        ];
        $batchProcesses = BatchProcesses::create($data);
        BatchProductJob::dispatch($file_path, $customer_id, $batchProcesses->id)->onQueue('batch_product')->onConnection('database');
    }

    /**
     * Get file
     * @param string $file_path
     * @return string|null content of file or null
     */
    public static function getFile($file_path)
    {
        $file = Storage::disk('public')->get($file_path);
        return $file;
    }
}
