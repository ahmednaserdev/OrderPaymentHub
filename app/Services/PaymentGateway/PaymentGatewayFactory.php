<?php

namespace App\Services\PaymentGateway;

use App\Services\PaymentGateway\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function create(string $gateway): PaymentGatewayInterface
    {
        return match (strtolower($gateway)) {
            'paypal' => new PaypalGateway(),
            'credit_card' => new CreditCardGateway(),
            default => throw new InvalidArgumentException('Invalid payment gateway selected'),
        };
    }
}
