<?php

use App\Models\User;

use function Pest\Laravel\postJson;

beforeEach(function () {


    $user = User::where('email', 'ahmed.naser@app.com')->first();
    if($user){
        $this->userData = [
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => '123456789',
        ];
    }else{
        $this->userData = [
            'name'     => 'Ahmed Naser',
            'email'    => 'ahmed.naser@app.com',
            'password' => '123456789',
        ];
        postJson('/api/auth/register', $this->userData)->assertStatus(201);
    }


    $this->loginResponse = postJson('/api/auth/login', [
        'email'    => $this->userData['email'],
        'password' => $this->userData['password'],
    ])->assertStatus(200);

    $this->token = $this->loginResponse['token'];
});

test('user can register', function () {
    $response = postJson('/api/auth/register', [
        'name'     => fake()->name(),
        'email'    => fake()->unique()->safeEmail(),
        'password' => 'password123',
    ]);

    $response->assertStatus(201);
});

test('user can login', function () {
    $response = postJson('/api/auth/login', [
        'email'    => $this->userData['email'],
        'password' => $this->userData['password'],
    ]);

    $response->assertStatus(200);
});


test('login validation fails', function () {
    $response = postJson('/api/auth/login', [
        'email'    => '',
        'password' => '',
    ]);

    $response->assertStatus(422);
});

test('registration validation fails', function () {
    $response = postJson('/api/auth/register', [
        'name'     => '',
        'email'    => 'invalid-email',
        'password' => '123',
    ]);

    $response->assertStatus(422);
});
