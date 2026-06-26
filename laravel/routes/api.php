<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureHasRole;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetSearchController;
use App\Http\Controllers\UserController;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;

Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('auth/register', [AuthController::class, 'register'])->middleware('auth:sanctum', EnsureHasRole::class.':admin');


Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

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
        Route::post('assign', [AssetController::class, 'assign']);
        Route::post('return', [AssetController::class, 'returnAsset']);
        // Route::get('{id}/history', [AssetController::class, 'history']);
        Route::get('recentlyAssigned', [AssetController::class, 'recentlyAssigned']);
        Route::get('allrecentlyAssigned', [AssetController::class, 'recentlyAllAssigned']);
        Route::get('all', [AssetController::class, 'showAll']);
        Route::get('history/{id}', [AssetController::class, 'AssetHistory']);
    });
});
Route::put('/putusers/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function(){ 
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
});
Route::get('/notifications', function () {
    return Notification::where('user_id', Auth::id())
        ->where('is_read', 0)
        // ->latest()
        ->get();
})->middleware('auth:sanctum');

Route::put('/notifications/{id}', function ($id) {
    $notification = Notification::findorFail($id);
    $notification->update([
        'is_read' => 1
    ]);

    return response()->json($notification);
})->middleware('auth:sanctum');;

Route::get('auth/role', [AuthController::class, 'getrole'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){ 
    Route::get('user/profile', [UserController::class, 'getProfile']);
    Route::put('user/profile', [UserController::class, 'updateProfile']);
    Route::put('user/password', [UserController::class, 'updatePassword']);
});

Route::get('search', [AssetSearchController::class, 'search'])->middleware('auth:sanctum');

