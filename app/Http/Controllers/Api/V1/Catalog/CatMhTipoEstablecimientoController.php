<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\StoreCatMhTipoEstablecimientoRequest;
use App\Http\Requests\Api\V1\Catalog\UpdateCatMhTipoEstablecimientoRequest;
use App\Http\Resources\Api\V1\CatMhTipoEstablecimientoResource;
use App\Models\CatMhTipoEstablecimiento;
use Illuminate\Http\JsonResponse;

class CatMhTipoEstablecimientoController extends Controller
{
    /**
     * GET /api/v1/catalog/tipo-establecimiento
     *
     * List all tipos de establecimiento.
     * Requires: auth:sanctum
     */
    public function index(): JsonResponse
    {
        $items = CatMhTipoEstablecimiento::orderBy('codigo')->get();

        return response()->json([
            'data' => CatMhTipoEstablecimientoResource::collection($items),
        ]);
    }

    /**
     * POST /api/v1/catalog/tipo-establecimiento
     *
     * Create a new tipo de establecimiento.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(StoreCatMhTipoEstablecimientoRequest $request): JsonResponse
    {
        $item = CatMhTipoEstablecimiento::create($request->validated());

        return response()->json([
            'message' => 'Tipo de establecimiento creado exitosamente.',
            'data'    => new CatMhTipoEstablecimientoResource($item),
        ], 201);
    }

    /**
     * PUT /api/v1/catalog/tipo-establecimiento/{tipo_establecimiento}
     *
     * Update a tipo de establecimiento.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(UpdateCatMhTipoEstablecimientoRequest $request, CatMhTipoEstablecimiento $tipo_establecimiento): JsonResponse
    {
        $tipo_establecimiento->update($request->validated());

        return response()->json([
            'message' => 'Tipo de establecimiento actualizado exitosamente.',
            'data'    => new CatMhTipoEstablecimientoResource($tipo_establecimiento),
        ]);
    }

    /**
     * DELETE /api/v1/catalog/tipo-establecimiento/{tipo_establecimiento}
     *
     * Soft-delete a tipo de establecimiento.
     * Requires: auth:sanctum + super_admin role.
     */
    public function destroy(CatMhTipoEstablecimiento $tipo_establecimiento): JsonResponse
    {
        $tipo_establecimiento->delete();

        return response()->json([
            'message' => 'Tipo de establecimiento eliminado exitosamente.',
        ]);
    }
}
