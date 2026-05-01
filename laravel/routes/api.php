<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureHasRole;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\AssetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('auth/register', [AuthController::class, 'register'])->middleware('auth:sanctum', EnsureHasRole::class.':admin');


Route::post('auth/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/category', [CategoryController::class, 'index']);
    Route::post('/category', [CategoryController::class, 'create']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories/{id}/parameters', [ParameterController::class, 'index']);
    Route::post('/categories/{id}/parameters', [ParameterController::class, 'create']);
    Route::put('/parameters/{id}', [ParameterController::class, 'update']);
    Route::delete('/parameters/{id}', [ParameterController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/assets', [AssetController::class, 'show']);
    Route::post('/assets', [AssetController::class, 'store']);
    Route::get('/assets/stats', [AssetController::class, 'stats']); 
    
    Route::get('/assets/{id}', [AssetController::class, 'index']);
    Route::put('/assets/{id}', [AssetController::class, 'update']);
    Route::delete('/assets/{id}', [AssetController::class, 'destroy']);
     
});

Route::get('auth/role', [AuthController::class, 'getrole'])->middleware('auth:sanctum');