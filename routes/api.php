<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SetupController;

// ========================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// ========================

// Información del sistema
Route::get('/system/info', [AuthController::class, 'systemInfo']);

// Health Check endpoints (sin autenticación para monitoreo externo)
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check']);
Route::get('/ping', [App\Http\Controllers\HealthController::class, 'ping']);

// Setup del sistema
Route::prefix('/pe/setup')->group(function () {
    Route::post('/migrate', [SetupController::class, 'migrate']);
    Route::post('/seed', [SetupController::class, 'seed']);
    Route::get('/status', [SetupController::class, 'status']);
});

// Inicialización del sistema
Route::post('pe/auth/initialize', [AuthController::class, 'initialize']);

// Autenticación
Route::post('/pe/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:auth');
