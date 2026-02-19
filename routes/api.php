<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Foodie SaaS
|--------------------------------------------------------------------------
|
| Prefix: /api  (automático de Laravel)
|
| Estructura:
|   /v1/auth/*      → autenticación pública
|   /v1/companies/* → gestión de empresas (solo super admin)
|
*/

// ─── Health check ─────────────────────────────────────────────────────────
Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'app'    => config('app.name'),
        'env'    => config('app.env'),
    ]);
});

// ─── v1 ───────────────────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // ── Autenticación ──────────────────────────────────────────────────────
    Route::prefix('auth')->name('auth.')->group(function () {

        // POST /api/v1/auth/login
        Route::post('/login', [AuthController::class, 'login'])
             ->name('login');

        // POST /api/v1/auth/super-admin  (solo si aún no existe ningún super admin)
        Route::post('/super-admin', [AuthController::class, 'registerSuperAdmin'])
             ->name('super-admin.register');

        // Rutas protegidas por token
        Route::middleware('auth:sanctum')->group(function () {

            // POST /api/v1/auth/logout
            Route::post('/logout', [AuthController::class, 'logout'])
                 ->name('logout');
        });
    });

    // ── Empresas (solo super admin) ────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'super_admin'])
         ->prefix('companies')
         ->name('companies.')
         ->group(function () {

             // POST /api/v1/companies
             Route::post('/', [CompanyController::class, 'store'])
                  ->name('store');
         });
});
