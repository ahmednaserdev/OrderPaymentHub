<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateway\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $payments = Payment::query();
        if ($request->has('order_id')) {
            $payments->where('order_id', $request->order_id);
        }
        $payments = $payments->paginate(10);
        return response()->json($payments);
    }

    public function processPayment(ProcessPaymentRequest $request): JsonResponse
    {
        $order = Order::findOrFail($request->order_id);
        $payment = $this->paymentService->process($order, $request->payment_method);
        return response()->json($payment, 201);
    }
}
