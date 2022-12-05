<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BuyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ConcertController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('logout', [AuthController::class, 'logout']);

    // Main Route
    Route::prefix('main')->group(function() {
        Route::post('buy', [BuyController::class, 'store']);
        Route::apiResource('akun', AccountController::class, ['only' => ['index']]);
        Route::apiResource('konser', ConcertController::class, ['only' => ['index', 'show']]);
        Route::apiResource('transaksi', TransactionController::class, ['only' => ['index', 'show']]);
    });

    // Admin Route
    Route::prefix('admin')->group(function() {
        Route::apiResource('akun', AccountController::class);
        Route::apiResource('konser', ConcertController::class);
        Route::apiResource('transaksi', TransactionController::class);
    });
});
