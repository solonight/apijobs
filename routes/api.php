<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User management routes
    Route::apiResource('users', UserController::class);
    Route::post('/users/{user}/assign-roles', [UserController::class, 'assignRoles']);
    Route::post('/users/{user}/give-permissions', [UserController::class, 'givePermissions']);

    // Job creation endpoint (only for users with permission)
    Route::middleware('auth:sanctum')->post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
    Route::delete('/applications/{id}', [ApplicationController::class,'destroy']);
    Route::post('/applications',[ApplicationController::class,'store']);
});