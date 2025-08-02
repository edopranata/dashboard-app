<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AvatarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::put('/', [AuthController::class, 'updateProfile']);
        Route::put('password', [AuthController::class, 'changePassword']);
    });

    // Avatar routes
    Route::prefix('avatar')->group(function () {
        Route::post('upload', [AvatarController::class, 'upload']);
        Route::delete('delete', [AvatarController::class, 'delete']);
        Route::get('show/{size?}', [AvatarController::class, 'show']);
    });

    // User management routes
    Route::apiResource('users', UserController::class);

    // Role management routes
    Route::apiResource('roles', RoleController::class);

    // Permission routes
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::get('permissions/grouped', [PermissionController::class, 'grouped']);

    // Dashboard routes
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
