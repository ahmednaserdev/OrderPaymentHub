<?php

namespace App\Services\PaymentGateway;

abstract class BasePaymentGateway implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->setCredentials();
    }

    /**
     * Set API credentials for the payment gateway.
     */
    abstract protected function setCredentials(): void;

    protected function executePaymentProcess(): array
    {
        return [
            'status' => 'successful',
            'transaction_id' => uniqid('txn_'),
            'message' => 'Payment processed successfully',
        ];
    }
}
