<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\Api\V1\ProductCategory\UpdateProductCategoryRequest;
use App\Http\Resources\Api\V1\ProductCategoryResource;
use App\Models\Company;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/product-categories
     *
     * Lista las categorías de productos de una empresa.
     */
    public function index(Request $request, Company $company): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 50);
        $perPage = min(max($perPage, 1), 200);

        $paginator = ProductCategory::where('company_id', $company->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => ProductCategoryResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem() ?? 0,
                'to'           => $paginator->lastItem()  ?? 0,
            ],
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/product-categories
     *
     * Crea una nueva categoría de productos para la empresa.
     */
    public function store(StoreProductCategoryRequest $request, Company $company): JsonResponse
    {
        $category = ProductCategory::create(array_merge(
            $request->validated(),
            ['company_id' => $company->id]
        ));

        return response()->json([
            'message' => 'Categoría creada exitosamente.',
            'data'    => new ProductCategoryResource($category),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/product-categories/{category}
     *
     * Actualiza una categoría de productos.
     */
    public function update(UpdateProductCategoryRequest $request, Company $company, ProductCategory $productCategory): JsonResponse
    {
        abort_if($productCategory->company_id !== $company->id, 404);

        $productCategory->update($request->validated());

        return response()->json([
            'message' => 'Categoría actualizada exitosamente.',
            'data'    => new ProductCategoryResource($productCategory->fresh()),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/product-categories/{category}
     *
     * Elimina (soft-delete) una categoría. Los productos conservan su referencia como null.
     */
    public function destroy(Company $company, ProductCategory $productCategory): JsonResponse
    {
        abort_if($productCategory->company_id !== $company->id, 404);

        $productCategory->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente.',
        ]);
    }
}
