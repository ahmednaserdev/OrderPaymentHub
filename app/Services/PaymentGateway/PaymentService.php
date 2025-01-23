<?php
namespace App\Services\PaymentGateway;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{

    public function process(Order $order, string $gateway)
    {
        return DB::transaction(function () use ($order, $gateway) {
            try {
                if ($order->status !== OrderStatus::CONFIRMED) {
                    throw new Exception('Payments can only be processed for confirmed orders', 422);
                }

                $paymentGateway  = PaymentGatewayFactory::create($gateway);
                $paymentResponse = $paymentGateway->processPayment($order);

                if ($paymentResponse['status'] !== 'successful') {
                    throw new Exception('There was an issue with the payment process. Please retry after verifying your information.', 422);
                }

                $order->update(['status' => 'confirmed']);
                $payment = Payment::create([
                    'order_id'       => $order->id,
                    'payment_id'     => $paymentResponse['transaction_id'],
                    'status'         => $paymentResponse['status'],
                    'payment_method' => $gateway,
                ]);

                DB::commit();
                return $payment;
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error processing payment: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}
