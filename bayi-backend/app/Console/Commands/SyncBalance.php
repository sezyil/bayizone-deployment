<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync balance of company customers.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Syncing balance of company customers...');
        $companyCustomers = \App\Models\CompanyCustomer\CompanyCustomer::all();
        $this->output->progressStart($companyCustomers->count());
        foreach ($companyCustomers as $companyCustomer) {
            $companyCustomer->calculateBalance();
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->info('Sync completed.');
    }
}
