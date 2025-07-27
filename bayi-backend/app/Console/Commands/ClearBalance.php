<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clear-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear balance of company customers.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Clearing balance of company customers...');
        $companyCustomers = \App\Models\CompanyCustomer\CompanyCustomer::all();
        $this->output->progressStart($companyCustomers->count());
        foreach ($companyCustomers as $companyCustomer) {
            $companyCustomer->clearBalance();
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->info('Clear completed.');
    }
}
