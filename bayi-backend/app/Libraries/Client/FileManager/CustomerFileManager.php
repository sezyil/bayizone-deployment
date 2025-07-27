<?php

namespace App\Libraries\Client\FileManager;

use Illuminate\Support\Facades\Storage;

class CustomerFileManager
{
    protected $customer_id;
    protected $customer_folder;
    protected $cache_folder;

    public function __construct(string $customer_id)
    {
        $this->customer_id = $customer_id;

        $this->createCustomerFolder();
        $this->createCacheFolder();
    }
    //generate customer image path

    private function createCustomerFolder()
    {
        $this->customer_folder = "uploads/client/{$this->customer_id}/";
        if (!Storage::disk('public')->exists($this->customer_folder)) {
            Storage::disk('public')->makeDirectory($this->customer_folder);
        }
    }

    //create cache folder
    private function createCacheFolder()
    {
        $this->cache_folder = $this->customer_folder . 'cache/';
        if (!Storage::disk('public')->exists($this->cache_folder)) {
            Storage::disk('public')->makeDirectory($this->cache_folder);
        }
    }

    //get customer folder
    public function getCustomerFolder($extra = null)
    {
        $folder = $this->customer_folder;
        if ($extra) {
            //check if extra has slash
            if (substr($extra, 0, 1) != '/') {
                $extra = '/' . $extra;
            }
            $folder .= $extra;
        }
        return $folder;
    }

    //get batch folder
    public function getBatchFolder($extra = null)
    {
        $folder = $this->customer_folder . 'batch';
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if ($extra) {
            //check if extra has slash
            if (substr($extra, 0, 1) != '/') {
                $extra = '/' . $extra;
            }
        }
        return $folder;
    }

    //get cache folder
    public function getCacheFolder()
    {
        return $this->cache_folder;
    }

    /**
     * Get customer profile image folder path
     *
     * @param string|null $file
     * @return string $folder | $folder . $file
     */
    public function getCustomerProfileImageFolder($file = null)
    {
        $folder = $this->customer_folder . 'profile/';
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if ($file) return $folder . $file;
        return $folder;
    }

    /**
     * Upload to cache folder
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public static function uploadToCacheFolder($customer_id, $file)
    {
        $instance = new self($customer_id);
        $path = $file->store($instance->getCacheFolder(), 'public');
        //remove double slashes
        return str_replace('//', '/', $path);
    }

    /**
     * upload directly to customer folder
     *
     * @param string $customer_id
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public static function uploadToCustomerFolder($customer_id, $file)
    {
        $instance = new self($customer_id);
        $path = $file->store($instance->getCustomerFolder(), 'public');
        //remove double slashes
        return str_replace('//', '/', $path);
    }
}
