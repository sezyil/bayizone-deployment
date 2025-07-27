<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        } */
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        //currencyformat directive
        \Blade::directive('currencyFormat', function ($expression, $currency = 'usd', $withSign = true) {
            return "<?= \App\Models\System\Currency::formatCurrency($expression, '$currency', $withSign); ?>";
        });

        Queue::after(function (JobProcessed $event) {
            $data = $event->job->getRawBody();
            $id = $event->job->getJobId();
            $status = $event->job->hasFailed() ? 'failed' : 'processed';
            $message = ucfirst($status) . ". Job $status. Job ID: $id. Data: $data";
            \Log::info($message);
        });

        Str::macro('lowerTurkish', function ($value) {

            $value = str_replace(['İ', 'I'], ['i', 'ı'], $value);

            return Str::lower($value);
        });

        Str::macro('upperTurkish', function ($value) {

            $value = str_replace(['i', 'ı'], ['İ', 'I'], $value);

            return Str::upper($value);
        });
    }
}
