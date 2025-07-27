<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Mail;
use Illuminate\Mail\Mailable;
class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Mailable $mail;

    public $timeout = 120;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::send($this->mail);
        $data = $this->job->getRawBody();
        Log::info("MailJob processed. Data: $data");
    }

    /**
     * The job failed to process.
     */
    public function failed()
    {
        $id= $this->job->getJobId();
        Log::error("MailJob failed. Job ID: $id");
    }
}
