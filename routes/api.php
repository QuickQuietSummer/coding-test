<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

Route::post('/register-client', [AuthController::class, 'registerClient']);

Route::post('/register-employee', [AuthController::class, 'registerEmployee']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/request-contact', [RequestController::class, 'contact']);

Route::resource('/requests', RequestController::class)->only(['index', 'store', 'update']);
Route::resource('/assignments', AssignmentController::class)->only(['store']);
