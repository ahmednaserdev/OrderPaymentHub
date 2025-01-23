<?php

namespace App\Providers;

use App\Services\PaymentGateway\PaypalGateway;
use App\Services\PaymentGateway\StripeGateway;
use App\Services\PaymentGateways\PaymentGatewayInterface;
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
        //
    }
}
