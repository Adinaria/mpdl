<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Role\RoleController;
use App\Http\Controllers\API\V1\User\UserController;

Route::group(['prefix' => '/v1'], function () {
    Route::group(['prefix' => '/auth'], function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::middleware('role:' . config('default_roles.administrator'))->group(function () {
            Route::group(['prefix' => '/roles'], function () {
                // можно было сделать через apiResource, но из-за мидлвара uuid пришлось разделить
                Route::get('/', [RoleController::class, 'index']);
                Route::post('/', [RoleController::class, 'store']);
                Route::group(['middleware' => 'uuid'], function () {
                    Route::get('{uuid}', [RoleController::class, 'show']);
                    Route::delete('{uuid}', [RoleController::class, 'destroy']);
                    Route::put('{uuid}', [RoleController::class, 'update']);
                    Route::patch('{uuid}', [RoleController::class, 'update']);
                });
            });
            Route::group(['prefix' => '/users'], function () {
                Route::get('/', [UserController::class, 'index']);
                Route::post('/', [UserController::class, 'store']);
                Route::group(['middleware' => 'uuid'], function () {
                    Route::get('{uuid}', [UserController::class, 'show']);
                    Route::delete('{uuid}', [UserController::class, 'destroy']);
                    Route::put('{uuid}', [UserController::class, 'updatePut']);
                    Route::patch('{uuid}', [UserController::class, 'updatePatch']);
                });
            });
        });
    });
});
