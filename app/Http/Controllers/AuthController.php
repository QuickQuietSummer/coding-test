<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\RegisterFormRequest;
use App\Services\Auth\LoginService;
use App\Services\Auth\RegisterService;
use Illuminate\Support\Facades\Response;

class AuthController
{
    private RegisterService $registerService;
    private LoginService $loginService;

    public function __construct(RegisterService $registerService, LoginService $loginService)
    {
        $this->registerService = $registerService;
        $this->loginService = $loginService;
    }

    /**
     * Login client.
     */
    public function login(LoginFormRequest $request)
    {
        $validatedData = $request->validated();

        $token = $this->loginService->login($validatedData['email'], $validatedData['password']);
        return Response::json([
            'message' => 'Logined',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    /**
     * Register client.
     */
    public function register(RegisterFormRequest $request)
    {
        $validatedData = $request->validated();

        $token = $this->registerService->registerClient($validatedData['name'], $validatedData['email'], $validatedData['password']);
        return Response::json([
            'message' => 'Registered',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }
}
