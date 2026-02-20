<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Requests\Api\V1\Product\UpdateProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/products
     *
     * List products for a company with pagination.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function index(Request $request, Company $company): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        $paginator = Product::with('unidadDeMedida')
            ->where('company_id', $company->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => ProductResource::collection($paginator->items()),
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
     * POST /api/v1/companies/{company}/products
     *
     * Create a new product for the given company.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function store(StoreProductRequest $request, Company $company): JsonResponse
    {
        $product = Product::create(array_merge(
            $request->validated(),
            ['company_id' => $company->id]
        ));

        return response()->json([
            'message' => 'Producto creado exitosamente.',
            'data'    => new ProductResource($product->load('unidadDeMedida')),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/products/{product}
     *
     * Update a product.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function update(UpdateProductRequest $request, Company $company, Product $product): JsonResponse
    {
        abort_if($product->company_id !== $company->id, 404);

        $product->update($request->validated());

        return response()->json([
            'message' => 'Producto actualizado exitosamente.',
            'data'    => new ProductResource($product->fresh()->load('unidadDeMedida')),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/products/{product}
     *
     * Soft-delete a product.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function destroy(Company $company, Product $product): JsonResponse
    {
        abort_if($product->company_id !== $company->id, 404);

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente.',
        ]);
    }
}
