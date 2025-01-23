<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $userData = $request->only(['name', 'email', 'password']);
            $response = $this->authService->register($userData);

            return response()->json($response, 201);
        } catch (Exception $e) {
            return $this->errorResponse('Registration failed', $e);
        }
    }


    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $response = $this->authService->login($credentials);

            return response()->json($response);
        } catch (Exception $e) {
            return $this->errorResponse('Login failed', $e);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json(['message' => 'Logged out successfully']);
        } catch (Exception $e) {
            return $this->errorResponse('Logout failed', $e);
        }
    }

    private function errorResponse(string $message, Exception $e): JsonResponse
    {
        return response()->json(['error' => $message, 'details' => $e->getMessage()], 500);
    }
}
