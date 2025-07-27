<?php

namespace App\Jobs\Client;

use App\Libraries\Client\FileManager\ProductFileManager;
use App\Libraries\Client\Product\Batch\ProductBatchCore;
use App\Libraries\Client\Product\Batch\ProductBatchCsv;
use App\Libraries\Client\Product\Batch\ProductBatchJson;
use App\Models\Catalog\Product\Product;
use App\Models\Catalog\Product\ProductDescription;
use App\Models\System\BatchProcesses;
use App\Models\System\Unit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * May be later will be refactored for better performance and error handling
 * Class BatchProductJob
 * @package App\Jobs\Client
 */
class BatchProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $filePath;
    public $customer_id;
    public $batchProcessId;
    private $errors = [];

    private $product_count = 0;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $customer_id, $batchId)
    {
        $this->batchProcessId = $batchId;
        $this->filePath = $filePath;
        $this->customer_id = $customer_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $batchProcess = BatchProcesses::where('id', $this->batchProcessId)->first();
        $payload = json_decode($batchProcess->payload, true);
        $file_path = $payload['file_path'];
        $file_type = $payload['file_type'];
        $file = ProductBatchCore::getFile($file_path);

        $batchProcess->is_working = true;
        $batchProcess->save();

        if (!$file) {
            Log::error('Dosya bulunamadı: ' . $this->filePath);
            $this->errors[] = 'Dosya Bulunamadı';
        } else {
            try {
                $this->fileResolver($file, $file_type);
            } catch (\Exception $exception) {
                //fire exception
                $this->failed($exception);
                return;
            }
        }

        if (count($this->errors) > 0) {
            $batchProcess->errors = json_encode($this->errors);
            $batchProcess->is_failed = true;
        }
        $batchProcess->is_completed = true;
        $batchProcess->is_working = false;
        //add payload count
        $payload['count'] = $this->product_count;
        $batchProcess->payload = json_encode($payload);
        $batchProcess->save();
        //info
        Log::info('Worker Id: ' . $this->batchProcessId . ' Customer Id: ' . $this->customer_id . ' File Path: ' . $this->filePath);
    }

    public function failed(\Exception $exception = null)
    {
        $error = $exception->getMessage();
        $batchProcess = BatchProcesses::where('id', $this->batchProcessId)->first();
        $batchProcess->is_failed = true;
        $batchProcess->is_working = false;
        $batchProcess->is_completed = true;
        //add to payload job id
        $payload = json_decode($batchProcess->payload, true);
        $payload['job_id'] = $this->job->getJobId();
        $payload['error'] = $error;
        $batchProcess->payload = json_encode($payload);
        $batchProcess->errors = ['Sistemsel bir hata oluştu. Lütfen tekrar deneyin. Eğer hata devam ederse destek ekibimizle iletişime geçin.'];
        $batchProcess->save();

        $error = $error . ' Worker Id: ' . $this->batchProcessId . ' Customer Id: ' . $this->customer_id . ' File Path: ' . $this->filePath. ' Caller File BatchProductJob.php';
        Log::error($error);
    }

    private function fileResolver(string $fileData, string $fileType = 'csv')
    {
        $productBatch = null;
        if ($fileType == 'csv') {
            $productBatch = new ProductBatchCsv($this->customer_id, $fileData);
        } else {
            $productBatch = new ProductBatchJson($this->customer_id, $fileData);
        }
        $productBatch->validate();
        if (count($productBatch->getErrors()) > 0) {
            $this->errors = array_merge($this->errors, $productBatch->getErrors());
        } else {
            $this->generateProduct($productBatch->getExtractedData());
        }
    }

    private function generateProduct($data)
    {
        $checkImageAvailable = function ($image) {
            //check image uri is available
            $headers = get_headers($image, 1);
            if (strpos($headers[0], '200')) {
                //check extension
                $extension = pathinfo($image, PATHINFO_EXTENSION);
                if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    return false;
                }
                return true;
            } else {
                return false;
            }
        };
        $product_count = 0;
        foreach ($data as $key => $data) {
            $product = new Product();
            $product->customer_id = $this->customer_id;
            $product->model = $data['model'];
            $product->sku = $data['sku'];
            $product->price = $data['price'];
            $product->quantity = $data['quantity'];
            $product->unit_id = Unit::where('short_name', 'adet')->first()->id;
            $product->minimum = $data['minimum'];
            $product->save();

            if (isset($data['images'])) {
                $firstImage = null;
                $productFileManager = new ProductFileManager($this->customer_id, $product->id);
                foreach ($data['images'] as $image) {
                    if ($checkImageAvailable($image)) {
                        $image = $productFileManager->downloadFromUrlAndStore($image);
                        if (!$firstImage) $firstImage = $image;
                        $product->images()->create([
                            'image' => $image,
                            'sort_order' => 0,
                        ]);
                    }
                }
                if (!$firstImage) {
                    $this->errors[] = 'Ürün resmi bulunamadı: ' . $data['name'];
                } else {
                    $product->image = $firstImage;
                }
                $product->save();
            }


            ProductDescription::create([
                'product_id' => $product->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'language' => 'tr',
            ]);
            $product_count++;
        }
        $this->product_count = $product_count;
    }
}
