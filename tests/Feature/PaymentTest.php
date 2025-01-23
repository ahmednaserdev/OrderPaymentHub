<?php

use App\Models\Order;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->loginResponse = postJson('/api/auth/login', [
        'email'    => 'ahmed.naser@app.com',
        'password' => '123456789',
    ])->assertStatus(200);

    $this->token = $this->loginResponse['token'];

    $this->orderData = [
        'items' => [
            ['product_name' => 'Product 1', 'quantity' => 2, 'price' => 50],
            ['product_name' => 'Product 2', 'quantity' => 1, 'price' => 100],
        ],
        'user_details' => [
            ['name' => 'Ahmed Naser', 'address' => '123 Main Street', 'phone' => '1234567890'],
        ],
        'total' => collect([
            ['quantity' => 2, 'price' => 50],
            ['quantity' => 1, 'price' => 100],
        ])->sum(fn($item) => $item['quantity'] * $item['price']),
    ];
});

test('user can view payments', function () {
    $response = getJson('/api/payments', ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(200);
});

test('user can view payments by order id', function () {
    $this->orderData['status'] = 'confirmed';
    $order = Order::create($this->orderData);

    postJson('/api/payments/process', [
        'order_id'       => $order->id,
        'payment_method' => 'credit_card',
    ], ['Authorization' => 'Bearer ' . $this->token]);

    $response = getJson("/api/payments?order_id={$order->id}", ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(200);
});

test('processing payment for confirmed order', function () {

    $this->orderData['status'] = 'confirmed';
    $order = Order::create($this->orderData);

    $response = postJson('/api/payments/process', [
        'order_id'       => $order->id,
        'payment_method' => 'credit_card',
    ], ['Authorization' => 'Bearer ' . $this->token]);

    $response->assertStatus(201);
});

test('processing payment fails for unconfirmed order', function () {
    $order    = Order::create($this->orderData);
    $response = postJson('/api/payments/process', [
        'order_id'       => $order->id,
        'payment_method' => 'credit_card',
    ], ['Authorization' => 'Bearer ' . $this->token]);

    $response->assertStatus(500);
});

test('payment validation fails', function () {
    $response = postJson('/api/payments/process', [
        'order_id'       => '',
        'payment_method' => '',
    ], ['Authorization' => 'Bearer ' . $this->token]);

    $response->assertStatus(422);
});
