<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\StoreCatMhActividadEconomicaRequest;
use App\Http\Requests\Api\V1\Catalog\UpdateCatMhActividadEconomicaRequest;
use App\Http\Resources\Api\V1\CatMhActividadEconomicaResource;
use App\Models\CatMhActividadEconomica;
use Illuminate\Http\JsonResponse;

class CatMhActividadEconomicaController extends Controller
{
    /**
     * GET /api/v1/catalog/economic-activities
     *
     * List all economic activities.
     * Requires: auth:sanctum
     */
    public function index(): JsonResponse
    {
        $activities = CatMhActividadEconomica::orderBy('codigo')->get();

        return response()->json([
            'data' => CatMhActividadEconomicaResource::collection($activities),
        ]);
    }

    /**
     * POST /api/v1/catalog/economic-activities
     *
     * Create a new economic activity.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(StoreCatMhActividadEconomicaRequest $request): JsonResponse
    {
        $activity = CatMhActividadEconomica::create($request->validated());

        return response()->json([
            'message' => 'Actividad económica creada exitosamente.',
            'data'    => new CatMhActividadEconomicaResource($activity),
        ], 201);
    }

    /**
     * PUT /api/v1/catalog/economic-activities/{actividad_economica}
     *
     * Update an economic activity.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(UpdateCatMhActividadEconomicaRequest $request, CatMhActividadEconomica $actividad_economica): JsonResponse
    {
        $actividad_economica->update($request->validated());

        return response()->json([
            'message' => 'Actividad económica actualizada exitosamente.',
            'data'    => new CatMhActividadEconomicaResource($actividad_economica),
        ]);
    }

    /**
     * DELETE /api/v1/catalog/economic-activities/{actividad_economica}
     *
     * Soft-delete an economic activity.
     * Requires: auth:sanctum + super_admin role.
     */
    public function destroy(CatMhActividadEconomica $actividad_economica): JsonResponse
    {
        $actividad_economica->delete();

        return response()->json([
            'message' => 'Actividad económica eliminada exitosamente.',
        ]);
    }
}
