<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Catalog\StoreCatMhMunicipioRequest;
use App\Http\Requests\Api\V1\Catalog\UpdateCatMhMunicipioRequest;
use App\Http\Resources\Api\V1\CatMhMunicipioResource;
use App\Models\CatMhMunicipio;
use Illuminate\Http\JsonResponse;

class CatMhMunicipioController extends Controller
{
    /**
     * GET /api/v1/catalog/municipios
     *
     * List all municipios.
     * Requires: auth:sanctum
     */
    public function index(): JsonResponse
    {
        $items = CatMhMunicipio::orderBy('codigo')->get();

        return response()->json([
            'data' => CatMhMunicipioResource::collection($items),
        ]);
    }

    /**
     * POST /api/v1/catalog/municipios
     *
     * Create a new municipio.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(StoreCatMhMunicipioRequest $request): JsonResponse
    {
        $item = CatMhMunicipio::create($request->validated());

        return response()->json([
            'message' => 'Municipio creado exitosamente.',
            'data'    => new CatMhMunicipioResource($item),
        ], 201);
    }

    /**
     * PUT /api/v1/catalog/municipios/{municipio}
     *
     * Update a municipio.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(UpdateCatMhMunicipioRequest $request, CatMhMunicipio $municipio): JsonResponse
    {
        $municipio->update($request->validated());

        return response()->json([
            'message' => 'Municipio actualizado exitosamente.',
            'data'    => new CatMhMunicipioResource($municipio),
        ]);
    }

    /**
     * DELETE /api/v1/catalog/municipios/{municipio}
     *
     * Soft-delete a municipio.
     * Requires: auth:sanctum + super_admin role.
     */
    public function destroy(CatMhMunicipio $municipio): JsonResponse
    {
        $municipio->delete();

        return response()->json([
            'message' => 'Municipio eliminado exitosamente.',
        ]);
    }
}
