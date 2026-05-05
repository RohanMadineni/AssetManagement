<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureHasRole;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;

Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('auth/register', [AuthController::class, 'register'])->middleware('auth:sanctum', EnsureHasRole::class.':admin');


Route::post('auth/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/category', [CategoryController::class, 'index']);
    Route::get('/category/{id}', [CategoryController::class, 'name']);
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
    Route::prefix('assets')->group(function () {
        Route::get('stats', [AssetController::class, 'stats']); 
        Route::get('allstats', [AssetController::class, 'allstats']); 
        Route::get('warranty/upcoming', [AssetController::class, 'upcomingWarranty']);
        Route::get('allwarranty/upcoming', [AssetController::class, 'upcomingAllWarranty']);
        Route::get('/', [AssetController::class, 'show']);
        Route::post('/', [AssetController::class, 'store']);
        Route::get('{id}', [AssetController::class, 'index'])->whereNumber('id');
        Route::put('{id}', [AssetController::class, 'update'])->whereNumber('id');
        Route::delete('{id}', [AssetController::class, 'destroy'])->whereNumber('id');
    });
    // Route::get('/assets/stats', [AssetController::class, 'stats']); 
    // Route::get('/assets/allstats', [AssetController::class, 'allstats']); 
    // Route::get('/assets', [AssetController::class, 'show']);
    // Route::post('/assets', [AssetController::class, 'store']);
    // Route::get('/assets/{id}', [AssetController::class, 'index'])->whereNumber('id');
    // Route::put('/assets/{id}', [AssetController::class, 'update'])->whereNumber('id');
    // Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->whereNumber('id');
});
Route::put('/putusers/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function(){ 
    Route::get('/users', [UserController::class, 'index']);
    
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
});

Route::get('auth/role', [AuthController::class, 'getrole'])->middleware('auth:sanctum');