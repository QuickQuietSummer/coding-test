<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->get('/requests', [RequestController::class, 'index']);
