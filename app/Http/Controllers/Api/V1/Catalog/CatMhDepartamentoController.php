<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\StoreCatMhDepartamentoRequest;
use App\Http\Requests\Api\V1\Catalog\UpdateCatMhDepartamentoRequest;
use App\Http\Resources\Api\V1\CatMhDepartamentoResource;
use App\Models\CatMhDepartamento;
use Illuminate\Http\JsonResponse;

class CatMhDepartamentoController extends Controller
{
    /**
     * GET /api/v1/catalog/departamentos
     *
     * List all departamentos.
     * Requires: auth:sanctum
     */
    public function index(): JsonResponse
    {
        $items = CatMhDepartamento::orderBy('codigo')->get();

        return response()->json([
            'data' => CatMhDepartamentoResource::collection($items),
        ]);
    }

    /**
     * POST /api/v1/catalog/departamentos
     *
     * Create a new departamento.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(StoreCatMhDepartamentoRequest $request): JsonResponse
    {
        $item = CatMhDepartamento::create($request->validated());

        return response()->json([
            'message' => 'Departamento creado exitosamente.',
            'data'    => new CatMhDepartamentoResource($item),
        ], 201);
    }

    /**
     * PUT /api/v1/catalog/departamentos/{departamento}
     *
     * Update a departamento.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(UpdateCatMhDepartamentoRequest $request, CatMhDepartamento $departamento): JsonResponse
    {
        $departamento->update($request->validated());

        return response()->json([
            'message' => 'Departamento actualizado exitosamente.',
            'data'    => new CatMhDepartamentoResource($departamento),
        ]);
    }

    /**
     * DELETE /api/v1/catalog/departamentos/{departamento}
     *
     * Soft-delete a departamento.
     * Requires: auth:sanctum + super_admin role.
     */
    public function destroy(CatMhDepartamento $departamento): JsonResponse
    {
        $departamento->delete();

        return response()->json([
            'message' => 'Departamento eliminado exitosamente.',
        ]);
    }
}
