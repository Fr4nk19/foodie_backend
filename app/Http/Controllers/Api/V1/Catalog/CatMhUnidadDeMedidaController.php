<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\StoreCatMhUnidadDeMedidaRequest;
use App\Http\Requests\Api\V1\Catalog\UpdateCatMhUnidadDeMedidaRequest;
use App\Http\Resources\Api\V1\CatMhUnidadDeMedidaResource;
use App\Models\CatMhUnidadDeMedida;
use Illuminate\Http\JsonResponse;

class CatMhUnidadDeMedidaController extends Controller
{
    /**
     * GET /api/v1/catalog/unidades-de-medida
     *
     * List all unidades de medida.
     * Requires: auth:sanctum
     */
    public function index(): JsonResponse
    {
        $items = CatMhUnidadDeMedida::orderBy('codigo')->get();

        return response()->json([
            'data' => CatMhUnidadDeMedidaResource::collection($items),
        ]);
    }

    /**
     * POST /api/v1/catalog/unidades-de-medida
     *
     * Create a new unidad de medida.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(StoreCatMhUnidadDeMedidaRequest $request): JsonResponse
    {
        $item = CatMhUnidadDeMedida::create($request->validated());

        return response()->json([
            'message' => 'Unidad de medida creada exitosamente.',
            'data'    => new CatMhUnidadDeMedidaResource($item),
        ], 201);
    }

    /**
     * PUT /api/v1/catalog/unidades-de-medida/{unidad_de_medida}
     *
     * Update an unidad de medida.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(UpdateCatMhUnidadDeMedidaRequest $request, CatMhUnidadDeMedida $unidad_de_medida): JsonResponse
    {
        $unidad_de_medida->update($request->validated());

        return response()->json([
            'message' => 'Unidad de medida actualizada exitosamente.',
            'data'    => new CatMhUnidadDeMedidaResource($unidad_de_medida),
        ]);
    }

    /**
     * DELETE /api/v1/catalog/unidades-de-medida/{unidad_de_medida}
     *
     * Soft-delete an unidad de medida.
     * Requires: auth:sanctum + super_admin role.
     */
    public function destroy(CatMhUnidadDeMedida $unidad_de_medida): JsonResponse
    {
        $unidad_de_medida->delete();

        return response()->json([
            'message' => 'Unidad de medida eliminada exitosamente.',
        ]);
    }
}
