<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Se estiver no Wasmer, força a execução da migration ao carregar a página
         if (env('WASMER_DATABASE_PASSWORD') || app()->environment('production')) {
              \Artisan::call('migrate', ['--force' => true]);
        }
    }
}
