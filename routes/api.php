<?php

use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Api\v1\PermissionController;
use App\Http\Controllers\Api\v1\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api'], function($routes){
    Route::group(['prefix' => 'auth'], function($routes){
        Route::post('login', [AuthenticationController::class, 'login']);
        Route::post('register', [AuthenticationController::class, 'register']);
        Route::post('refresh', [AuthenticationController::class, 'refresh']);
        Route::post('logout', [AuthenticationController::class, 'logout']);
        Route::get('me', [AuthenticationController::class, 'getaccount'])->middleware('permission:store-user');
    });

    Route::post('permission/update-status', [PermissionController::class, 'changeStatus']);
    Route::post('role/update-status', [RoleController::class, 'changeStatus']);
});
