<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Catalog\CatMhActividadEconomicaController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CompanyEconomicActivityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Foodie SaaS
|--------------------------------------------------------------------------
|
| Prefix: /api  (automático de Laravel)
|
| Estructura:
|   /v1/auth/*                                → autenticación pública
|   /v1/companies/*                           → gestión de empresas (solo super admin)
|   /v1/catalog/economic-activities/*         → catálogo MH (solo super admin)
|   /v1/companies/{company}/economic-activities/* → actividades por empresa
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

             // GET  /api/v1/companies
             Route::get('/', [CompanyController::class, 'index'])
                  ->name('index');

             // POST /api/v1/companies
             Route::post('/', [CompanyController::class, 'store'])
                  ->name('store');
         });

    // ── Catálogo de actividades económicas MH ─────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('catalog/economic-activities')
         ->name('catalog.economic-activities.')
         ->group(function () {

             // GET /api/v1/catalog/economic-activities  (todos los autenticados)
             Route::get('/', [CatMhActividadEconomicaController::class, 'index'])
                  ->name('index');

             // Solo super admin puede crear/editar/eliminar del catálogo
             Route::middleware('super_admin')->group(function () {

                 // POST /api/v1/catalog/economic-activities
                 Route::post('/', [CatMhActividadEconomicaController::class, 'store'])
                      ->name('store');

                 // PUT /api/v1/catalog/economic-activities/{actividad_economica}
                 Route::put('/{actividad_economica}', [CatMhActividadEconomicaController::class, 'update'])
                      ->name('update');

                 // DELETE /api/v1/catalog/economic-activities/{actividad_economica}
                 Route::delete('/{actividad_economica}', [CatMhActividadEconomicaController::class, 'destroy'])
                      ->name('destroy');
             });
         });

    // ── Actividades económicas por empresa ────────────────────────────────
    Route::middleware(['auth:sanctum', 'company_admin_or_super'])
         ->prefix('companies/{company}/economic-activities')
         ->name('companies.economic-activities.')
         ->group(function () {

             // GET /api/v1/companies/{company}/economic-activities
             Route::get('/', [CompanyEconomicActivityController::class, 'index'])
                  ->name('index');

             // POST /api/v1/companies/{company}/economic-activities
             Route::post('/', [CompanyEconomicActivityController::class, 'store'])
                  ->name('store');

             // PATCH /api/v1/companies/{company}/economic-activities/{economicActivity}
             Route::patch('/{economicActivity}', [CompanyEconomicActivityController::class, 'update'])
                  ->name('update');

             // DELETE /api/v1/companies/{company}/economic-activities/{economicActivity}
             Route::delete('/{economicActivity}', [CompanyEconomicActivityController::class, 'destroy'])
                  ->name('destroy');
         });
});
