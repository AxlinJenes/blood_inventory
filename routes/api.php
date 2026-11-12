<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BloodBagController;
use App\Http\Controllers\Api\TemperatureAnalysisController;
use App\Http\Controllers\Api\DashboardController;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {

        Route::get('/user', [AuthController::class, 'user']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {

    Route::get('/admin/test', function () {
        return response()->json([
            'message' => 'Welcome Admin',
        ]);
    });

});

Route::middleware(['auth:api', 'role:blood_bank_staff'])->group(function () {

    Route::get('/staff/test', function () {
        return response()->json([
            'message' => 'Welcome Blood Bank Staff',
        ]);
    });

});

Route::middleware(['auth:api', 'role:monitoring_user'])->group(function () {

    Route::get('/monitoring/test', function () {
        return response()->json([
            'message' => 'Welcome Monitoring User',
        ]);
    });

});

Route::middleware('auth:api')->group(function () {

    Route::get('/blood-bags', [BloodBagController::class, 'index']);
    Route::get('/blood-bags/expiring-within-24-hours', [BloodBagController::class, 'expiringWithin24Hours']);
    Route::get('/blood-bags/expired', [BloodBagController::class, 'expiredBloodBags']);
    Route::get('/blood-bags/near-risk', [BloodBagController::class, 'nearRiskInventory']);
    Route::get('/blood-bags/{id}', [BloodBagController::class, 'show']);
    Route::post('/blood-bags', [BloodBagController::class, 'store']);
    Route::put('/blood-bags/{id}', [BloodBagController::class, 'update']);
    Route::delete('/blood-bags/{id}', [BloodBagController::class, 'destroy']);
});

Route::get(
    '/refrigerators/{refrigeratorId}/daily-statistics',
    [TemperatureAnalysisController::class, 'dailyStatistics']
);

Route::get('/dashboard', [DashboardController::class, 'index']);