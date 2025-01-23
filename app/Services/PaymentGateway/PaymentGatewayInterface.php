<?php

namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    public function processPayment($order): array;
}


