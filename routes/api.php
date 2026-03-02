<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\Catalog\CatMhActividadEconomicaController;
use App\Http\Controllers\Api\V1\Catalog\CatMhTipoEstablecimientoController;
use App\Http\Controllers\Api\V1\Catalog\CatMhDepartamentoController;
use App\Http\Controllers\Api\V1\Catalog\CatMhMunicipioController;
use App\Http\Controllers\Api\V1\Catalog\CatMhUnidadDeMedidaController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CompanyEconomicActivityController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\TableZoneController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\KitchenController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Foodie SaaS
|--------------------------------------------------------------------------
|
| Prefix: /api  (automático de Laravel)
|
| Estructura:
|   /v1/auth/*                                    → autenticación pública
|   /v1/companies/*                               → gestión de empresas (solo super admin)
|   /v1/companies/{company}/branches/*            → sucursales por empresa (super admin)
|   /v1/catalog/economic-activities/*             → catálogo MH (super admin)
|   /v1/catalog/tipo-establecimiento/*            → catálogo tipo establecimiento (super admin)
|   /v1/catalog/departamentos/*                   → catálogo departamentos (super admin)
|   /v1/catalog/municipios/*                      → catálogo municipios (super admin)
|   /v1/companies/{company}/economic-activities/* → actividades por empresa
|   /v1/users/*                                   → usuarios globales (super admin)
|   /v1/companies/{company}/users/*               → usuarios por empresa
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

             // GET  /api/v1/companies/{company}
             Route::get('/{company}', [CompanyController::class, 'show'])
                  ->name('show');

             // PUT  /api/v1/companies/{company}
             Route::put('/{company}', [CompanyController::class, 'update'])
                  ->name('update');

             // ── Sucursales por empresa ─────────────────────────────────────
             Route::prefix('/{company}/branches')
                  ->name('branches.')
                  ->group(function () {

                      // POST   /api/v1/companies/{company}/branches
                      Route::post('/', [BranchController::class, 'store'])
                           ->name('store');

                      // PUT    /api/v1/companies/{company}/branches/{branch}
                      Route::put('/{branch}', [BranchController::class, 'update'])
                           ->name('update');

                      // DELETE /api/v1/companies/{company}/branches/{branch}
                      Route::delete('/{branch}', [BranchController::class, 'destroy'])
                           ->name('destroy');
                  });
         });




   
     // ── Sucursales por empresa (company_admin o super admin: solo lectura) ─
     Route::middleware(['auth:sanctum', 'company_admin_or_super'])
         ->prefix('companies/{company}/branches')
         ->name('companies.branches.')
         ->group(function () {
             // GET /api/v1/companies/{company}/branches
             Route::get('/', [BranchController::class, 'index'])->name('index');

             // GET /api/v1/companies/{company}/branches/{branch}
             Route::get('/{branch}', [BranchController::class, 'show'])->name('show');
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

    // ── Catálogo de tipos de establecimiento MH ───────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('catalog/tipo-establecimiento')
         ->name('catalog.tipo-establecimiento.')
         ->group(function () {

             // GET /api/v1/catalog/tipo-establecimiento  (todos los autenticados)
             Route::get('/', [CatMhTipoEstablecimientoController::class, 'index'])
                  ->name('index');

             // Solo super admin puede crear/editar/eliminar del catálogo
             Route::middleware('super_admin')->group(function () {

                 // POST /api/v1/catalog/tipo-establecimiento
                 Route::post('/', [CatMhTipoEstablecimientoController::class, 'store'])
                      ->name('store');

                 // PUT /api/v1/catalog/tipo-establecimiento/{tipo_establecimiento}
                 Route::put('/{tipo_establecimiento}', [CatMhTipoEstablecimientoController::class, 'update'])
                      ->name('update');

                 // DELETE /api/v1/catalog/tipo-establecimiento/{tipo_establecimiento}
                 Route::delete('/{tipo_establecimiento}', [CatMhTipoEstablecimientoController::class, 'destroy'])
                      ->name('destroy');
             });
         });

    // ── Catálogo de departamentos MH ──────────────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('catalog/departamentos')
         ->name('catalog.departamentos.')
         ->group(function () {

             // GET /api/v1/catalog/departamentos  (todos los autenticados)
             Route::get('/', [CatMhDepartamentoController::class, 'index'])
                  ->name('index');

             // Solo super admin puede crear/editar/eliminar del catálogo
             Route::middleware('super_admin')->group(function () {

                 // POST /api/v1/catalog/departamentos
                 Route::post('/', [CatMhDepartamentoController::class, 'store'])
                      ->name('store');

                 // PUT /api/v1/catalog/departamentos/{departamento}
                 Route::put('/{departamento}', [CatMhDepartamentoController::class, 'update'])
                      ->name('update');

                 // DELETE /api/v1/catalog/departamentos/{departamento}
                 Route::delete('/{departamento}', [CatMhDepartamentoController::class, 'destroy'])
                      ->name('destroy');
             });
         });

    // ── Catálogo de municipios MH ─────────────────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('catalog/municipios')
         ->name('catalog.municipios.')
         ->group(function () {

             // GET /api/v1/catalog/municipios  (todos los autenticados)
             Route::get('/', [CatMhMunicipioController::class, 'index'])
                  ->name('index');

             // Solo super admin puede crear/editar/eliminar del catálogo
             Route::middleware('super_admin')->group(function () {

                 // POST /api/v1/catalog/municipios
                 Route::post('/', [CatMhMunicipioController::class, 'store'])
                      ->name('store');

                 // PUT /api/v1/catalog/municipios/{municipio}
                 Route::put('/{municipio}', [CatMhMunicipioController::class, 'update'])
                      ->name('update');

                 // DELETE /api/v1/catalog/municipios/{municipio}
                 Route::delete('/{municipio}', [CatMhMunicipioController::class, 'destroy'])
                      ->name('destroy');
             });
         });

    // ── Catálogo de unidades de medida MH ────────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('catalog/unidades-de-medida')
         ->name('catalog.unidades-de-medida.')
         ->group(function () {

             // GET /api/v1/catalog/unidades-de-medida  (todos los autenticados)
             Route::get('/', [CatMhUnidadDeMedidaController::class, 'index'])
                  ->name('index');

             // Solo super admin puede crear/editar/eliminar del catálogo
             Route::middleware('super_admin')->group(function () {

                 // POST /api/v1/catalog/unidades-de-medida
                 Route::post('/', [CatMhUnidadDeMedidaController::class, 'store'])
                      ->name('store');

                 // PUT /api/v1/catalog/unidades-de-medida/{unidad_de_medida}
                 Route::put('/{unidad_de_medida}', [CatMhUnidadDeMedidaController::class, 'update'])
                      ->name('update');

                 // DELETE /api/v1/catalog/unidades-de-medida/{unidad_de_medida}
                 Route::delete('/{unidad_de_medida}', [CatMhUnidadDeMedidaController::class, 'destroy'])
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

    // ── Usuarios globales (solo super admin) ──────────────────────────────
    Route::middleware(['auth:sanctum', 'super_admin'])
         ->prefix('users')
         ->name('users.')
         ->group(function () {

             // GET  /api/v1/users
             Route::get('/',       [UserController::class, 'index'])->name('index');

             // POST /api/v1/users
             Route::post('/',      [UserController::class, 'store'])->name('store');

             // GET  /api/v1/users/{user}
             Route::get('/{user}', [UserController::class, 'show'])->name('show');

             // PUT  /api/v1/users/{user}
             Route::put('/{user}', [UserController::class, 'update'])->name('update');

             // DELETE /api/v1/users/{user}
             Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
         });

    // ── Usuarios por empresa (company_admin o super admin) ────────────────
    Route::middleware(['auth:sanctum', 'company_admin_or_super'])
         ->prefix('companies/{company}/users')
         ->name('companies.users.')
         ->group(function () {

             // GET  /api/v1/companies/{company}/users
             Route::get('/',       [UserController::class, 'indexByCompany'])->name('index');

             // POST /api/v1/companies/{company}/users
             Route::post('/',      [UserController::class, 'storeForCompany'])->name('store');

             // PUT  /api/v1/companies/{company}/users/{user}
             Route::put('/{user}', [UserController::class, 'update'])->name('update');

             // DELETE /api/v1/companies/{company}/users/{user}
             Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
         });

    // ── Categorías de productos por empresa (company_admin o super admin) ────
    Route::middleware(['auth:sanctum', 'company_admin_or_super'])
         ->prefix('companies/{company}/product-categories')
         ->name('companies.product-categories.')
         ->group(function () {

             // GET    /api/v1/companies/{company}/product-categories
             Route::get('/', [ProductCategoryController::class, 'index'])->name('index');

             // GET    /api/v1/companies/{company}/product-categories/{productCategory}
             Route::get('/{productCategory}', [ProductCategoryController::class, 'show'])->name('show');

             // POST   /api/v1/companies/{company}/product-categories
             Route::post('/', [ProductCategoryController::class, 'store'])->name('store');

             // PUT    /api/v1/companies/{company}/product-categories/{productCategory}
             Route::put('/{productCategory}', [ProductCategoryController::class, 'update'])->name('update');

             // DELETE /api/v1/companies/{company}/product-categories/{productCategory}
             Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('destroy');
         });

    // ── Productos por empresa (company_admin o super admin) ───────────────
    Route::middleware(['auth:sanctum', 'company_admin_or_super'])
         ->prefix('companies/{company}/products')
         ->name('companies.products.')
         ->group(function () {

             // GET /api/v1/companies/{company}/products
             Route::get('/', [ProductController::class, 'index'])->name('index');

             // GET /api/v1/companies/{company}/products/{product}
             Route::get('/{product}', [ProductController::class, 'show'])->name('show');

             // POST /api/v1/companies/{company}/products
             Route::post('/', [ProductController::class, 'store'])->name('store');

             // PUT /api/v1/companies/{company}/products/{product}
             Route::put('/{product}', [ProductController::class, 'update'])->name('update');

             // DELETE /api/v1/companies/{company}/products/{product}
             Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
         });

    // ── Inventario por sucursal (company_admin o super admin) ─────────────
    Route::middleware(['auth:sanctum', 'company_admin_or_super'])
         ->prefix('companies/{company}/branches/{branch}/inventory')
         ->name('companies.branches.inventory.')
         ->group(function () {

             // GET /api/v1/companies/{company}/branches/{branch}/inventory
             Route::get('/', [InventoryController::class, 'index'])->name('index');

             // GET /api/v1/companies/{company}/branches/{branch}/inventory/{inventory}
             Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');

             // POST /api/v1/companies/{company}/branches/{branch}/inventory
             Route::post('/', [InventoryController::class, 'store'])->name('store');

             // PUT /api/v1/companies/{company}/branches/{branch}/inventory/{inventory}
             Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');

             // DELETE /api/v1/companies/{company}/branches/{branch}/inventory/{inventory}
             Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
         });

    // ── Mesas por sucursal (autenticados) ──────────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('companies/{company}/branches/{branch}/tables')
         ->name('companies.branches.tables.')
         ->group(function () {

             // GET    /api/v1/companies/{company}/branches/{branch}/tables
             Route::get('/', [TableController::class, 'index'])->name('index');

             // GET    /api/v1/companies/{company}/branches/{branch}/tables/{table}
             Route::get('/{table}', [TableController::class, 'show'])->name('show');

             // POST   /api/v1/companies/{company}/branches/{branch}/tables
             Route::post('/', [TableController::class, 'store'])->name('store');

             // PUT    /api/v1/companies/{company}/branches/{branch}/tables/{table}
             Route::put('/{table}', [TableController::class, 'update'])->name('update');

             // DELETE /api/v1/companies/{company}/branches/{branch}/tables/{table}
             Route::delete('/{table}', [TableController::class, 'destroy'])->name('destroy');
         });

    // ── Pedidos por sucursal (autenticados) ────────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('companies/{company}/branches/{branch}/orders')
         ->name('companies.branches.orders.')
         ->group(function () {

             // GET    /api/v1/companies/{company}/branches/{branch}/orders
             Route::get('/', [OrderController::class, 'index'])->name('index');

             // GET    /api/v1/companies/{company}/branches/{branch}/orders/{order}
             Route::get('/{order}', [OrderController::class, 'show'])->name('show');

             // POST   /api/v1/companies/{company}/branches/{branch}/orders
             Route::post('/', [OrderController::class, 'store'])->name('store');

             // PUT    /api/v1/companies/{company}/branches/{branch}/orders/{order}
             Route::put('/{order}', [OrderController::class, 'update'])->name('update');

             // PATCH  /api/v1/companies/{company}/branches/{branch}/orders/{order}/status
             Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('status');

             // PATCH  /api/v1/companies/{company}/branches/{branch}/orders/{order}/items/{item}/toggle
             Route::patch('/{order}/items/{item}/toggle', [OrderController::class, 'toggleItem'])->name('items.toggle');

             // DELETE /api/v1/companies/{company}/branches/{branch}/orders/{order}
             Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
         });

    // ── Zonas de mesas por sucursal (autenticados) ────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('companies/{company}/branches/{branch}/zones')
         ->name('companies.branches.zones.')
         ->group(function () {

             // GET    /api/v1/companies/{company}/branches/{branch}/zones
             Route::get('/', [TableZoneController::class, 'index'])->name('index');

             // GET    /api/v1/companies/{company}/branches/{branch}/zones/{zone}
             Route::get('/{zone}', [TableZoneController::class, 'show'])->name('show');

             // POST   /api/v1/companies/{company}/branches/{branch}/zones
             Route::post('/', [TableZoneController::class, 'store'])->name('store');

             // PUT    /api/v1/companies/{company}/branches/{branch}/zones/{zone}
             Route::put('/{zone}', [TableZoneController::class, 'update'])->name('update');

             // DELETE /api/v1/companies/{company}/branches/{branch}/zones/{zone}
             Route::delete('/{zone}', [TableZoneController::class, 'destroy'])->name('destroy');
         });

    // ── Módulo de Cocina por sucursal (autenticados) ───────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('companies/{company}/branches/{branch}/kitchen')
         ->name('companies.branches.kitchen.')
         ->group(function () {

             // GET   /api/v1/companies/{company}/branches/{branch}/kitchen/orders
             Route::get('/orders', [KitchenController::class, 'orders'])->name('orders');

             // PATCH /api/v1/companies/{company}/branches/{branch}/kitchen/orders/{order}/status
             Route::patch('/orders/{order}/status', [KitchenController::class, 'updateOrderStatus'])->name('orders.status');

             // PATCH /api/v1/companies/{company}/branches/{branch}/kitchen/orders/{order}/items/{item}/toggle
             Route::patch('/orders/{order}/items/{item}/toggle', [KitchenController::class, 'toggleItem'])->name('orders.items.toggle');

             // GET   /api/v1/companies/{company}/branches/{branch}/kitchen/stats
             Route::get('/stats', [KitchenController::class, 'stats'])->name('stats');
         });

    // ── Dashboard por sucursal (autenticados) ──────────────────────────────
    Route::middleware(['auth:sanctum'])
         ->prefix('companies/{company}/branches/{branch}')
         ->name('companies.branches.')
         ->group(function () {

             // GET /api/v1/companies/{company}/branches/{branch}/dashboard
             Route::get('/dashboard', [DashboardController::class, 'stats'])->name('dashboard');
         });
});
