<?php

use App\Models\Order;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;


beforeEach(function () {
    $this->loginResponse = postJson('/api/auth/login', [
        'email' => 'ahmed.naser@app.com',
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

test('user can view orders', function () {
    $response = getJson('/api/orders', ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(200);
});

test('user can view orders filter by status', function () {
    $response = getJson('/api/orders?status=pending', ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(200);
});

test('user can create order', function () {
    $response = postJson('/api/orders', $this->orderData, ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(201);
});

test('user can update order', function () {
    $order = Order::create($this->orderData);
    $updateData = [
        'items' => [
            [
            'product_name' => 'Updated Product',
            'quantity' => 3,
            'price' => 30
            ]
        ],
    ];

    $response = putJson("/api/orders/{$order->id}", $updateData, ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(200);
});

test('user can delete order', function () {
    $order = Order::where('status', 'pending')->inRandomOrder()->first();
    $response = deleteJson("/api/orders/{$order->id}", [], ['Authorization' => 'Bearer ' . $this->token]);
    $response->assertStatus(200);;
});

test('order validation fails', function () {
    $response = postJson('/api/orders', [
        'items'        => [['quantity' => 1, 'price' => 50]],
        'user_details' => [['address' => '123 Main Street', 'phone' => '1234567890']],
    ], ['Authorization' => 'Bearer ' . $this->token]);

    $response->assertStatus(422);
});
