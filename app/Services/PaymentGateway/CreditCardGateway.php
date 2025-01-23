<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\BasePaymentGateway;

class CreditCardGateway extends BasePaymentGateway
{
    protected function setCredentials(): void
    {
        $this->apiKey = env('STRIPE_API_KEY');
        $this->apiSecret = env('STRIPE_API_SECRET');
    }

    private function authenticate(): bool
    {
        // true => Success authenticate
        // false => Not Success authenticate
        return true;
    }

    public function processPayment($order): array
    {
        if (!$this->authenticate()) {
            return ['status' => 'failed', 'message' => 'Invalid credentials', 'transaction_id' => null];
        }
        return $this->executePaymentProcess();
    }
}
