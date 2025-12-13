<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\AssetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
})->middleware('auth:sanctum');


Route::apiResource('posts', PostController::class);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:api')->group(function() {
    Route::get('/categories',[CategoryController::class,'index']);
    Route::post('/categories',[CategoryController::class,'store'])->middleware('role:admin,manager');
    Route::put('/categories/{id}',[CategoryController::class,'update'])->middleware('role:admin,manager');
    Route::delete('/categories/{id}',[CategoryController::class,'destroy'])->middleware('role:admin');
});

Route::middleware('auth:api')->group(function() {
    Route::get('/categories/{id}/parameters',[ParameterController::class,'index']);
    Route::post('/categories/{id}/parameters',[ParameterController::class,'store'])->middleware('role:admin,manager');
    Route::put('/parameters/{id}',[ParameterController::class,'update'])->middleware('role:admin,manager');
    Route::delete('/parameters/{id}',[ParameterController::class,'destroy'])->middleware('role:admin');
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/assets',[AssetController::class, 'index']);
    Route::post('/assets',[AssetController::class, 'store'])->middleware('role:admin,manager');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->middleware('role:admin,manager');
    Route::delete('assets/{id}', [AssetController::class, 'destroy'])->middleware('role:admin');
});