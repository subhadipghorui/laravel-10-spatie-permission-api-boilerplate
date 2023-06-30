<?php

use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Api\v1\PermissionController;
use App\Http\Controllers\Api\v1\RoleController;
use App\Http\Controllers\Api\v1\UserManagementController;
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

Route::group(['prefix' => 'auth'], function($routes){
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class, 'register']);
    Route::post('refresh', [AuthenticationController::class, 'refresh']);
    Route::post('logout', [AuthenticationController::class, 'logout']);
    Route::get('me', [AuthenticationController::class, 'getaccount'])->middleware('permission:store-permission');
});

Route::group(['middleware' => 'auth'], function($routes){
    Route::get('permissions', [PermissionController::class, 'index'])->middleware('permission:list-permission');
    Route::post('permission/update-status', [PermissionController::class, 'changeStatus'])->middleware('permission:update-permission');

    Route::get('roles', [RoleController::class, 'index'])->middleware('permission:list-role');
    Route::post('role/update-status', [RoleController::class, 'changeStatus'])->middleware('permission:update-role');

    // User management
    Route::group(['prefix' => 'users', 'middleware' => 'auth'], function($routes){
        Route::get('/', [UsermanagementController::class, 'index'])->middleware('permission:list-permission');
        Route::post('/', [UsermanagementController::class, 'store'])->middleware('permission:store-permission');
        Route::get('/{id}', [UsermanagementController::class, 'show'])->middleware('permission:view-permission');
        Route::put('/{id}', [UsermanagementController::class, 'update'])->middleware('permission:update-permission');
        Route::delete('/{id}', [UsermanagementController::class, 'destroy'])->middleware('permission:delete-permission');
        Route::post('/asssign-permissions', [UserManagementController::class, 'assignPermissions'])->middleware('permission:update-permission');
    });
});