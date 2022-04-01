<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
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
     * Login
     * @group Auth
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
     * Register client
     * @group Auth
     */
    public function registerClient(RegisterFormRequest $request)
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

    /**
     * Register employee
     * @group Auth
     */
    public function registerEmployee(RegisterFormRequest $request)
    {
        $validatedData = $request->validated();

        $token = $this->registerService->registerEmployee($validatedData['name'], $validatedData['email'], $validatedData['password']);
        return Response::json([
            'message' => 'Registered',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }
}
