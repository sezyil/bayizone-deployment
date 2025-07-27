<?php

namespace App\Console\Commands;

use App\Models\System\Currency;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currencies from the API';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ///show log in console
        try {
            $this->info('Updating currencies...');
            $this->updateCurrencies();
            $this->info('Currencies updated successfully');

            $this->info('TEST CURRENCY CONVERT');
            $amount = 100;
            $from = Currency::CODE_USD;
            $to = Currency::CODE_TL;
            $converted = Currency::convert($amount, $from, $to);
            $this->info($amount . ' ' . $from . ' = ' . $converted . ' ' . $to);


        } catch (\Exception $e) {
            $this->error('An error occurred while updating currencies: ' . $e->getMessage());
        }
    }

    private function updateCurrencies(): void
    {
        //check simplexml_load_file is active
        if (!function_exists('simplexml_load_file')) {
            throw new \Exception('simplexml_load_file function is not active');
        }

        $doviz = simplexml_load_file('http://www.tcmb.gov.tr/kurlar/today.xml');

        $dolar_satis = $doviz->Currency[0]->BanknoteSelling;
        $euro_satis = $doviz->Currency[3]->BanknoteSelling;
        $pound_satis = $doviz->Currency[4]->BanknoteSelling;

        $calculated_dollar = 1 * $dolar_satis;
        $calculated_euro = 1 * $euro_satis;
        $calculated_pound = 1 * $pound_satis;


        if ((float)$calculated_pound > 0) {
            Currency::where('code', Currency::CODE_GBP)
                ->where('rate', '>', 0.01)
                ->update([
                    'rate' => $calculated_pound,
                    'updated_at' => now()
                ]);
        }
        if ((float)$calculated_dollar > 0) {
            Currency::where('code', Currency::CODE_USD)
                ->where('rate', '>', 0.01)
                ->update([
                    'rate' => $calculated_dollar,
                    'updated_at' => now()
                ]);
        }
        if ((float)$calculated_euro > 0) {
            Currency::where('code', Currency::CODE_EUR)
                ->where('rate', '>', 0.01)
                ->update([
                    'rate' => $calculated_euro,
                    'updated_at' => now()
                ]);
        }
    }
}
