<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Inventory\StoreInventoryRequest;
use App\Http\Requests\Api\V1\Inventory\UpdateInventoryRequest;
use App\Http\Resources\Api\V1\InventoryResource;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches/{branch}/inventory
     *
     * List inventory items for a branch with pagination.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function index(Request $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        $paginator = Inventory::with('product.unidadDeMedida')
            ->where('branch_id', $branch->id)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => InventoryResource::collection($paginator->items()),
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
     * GET /api/v1/companies/{company}/branches/{branch}/inventory/{inventory}
     *
     * Show a single inventory item.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function show(Company $company, Branch $branch, Inventory $inventory): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($inventory->branch_id !== $branch->id, 404);

        return response()->json([
            'data' => new InventoryResource($inventory->load('product.unidadDeMedida')),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/branches/{branch}/inventory
     *
     * Add a product to the branch inventory.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function store(StoreInventoryRequest $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $item = Inventory::create(array_merge(
            $request->validated(),
            ['branch_id' => $branch->id]
        ));

        return response()->json([
            'message' => 'Producto agregado al inventario exitosamente.',
            'data'    => new InventoryResource($item->load('product.unidadDeMedida')),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/branches/{branch}/inventory/{inventory}
     *
     * Update an inventory item.
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function update(UpdateInventoryRequest $request, Company $company, Branch $branch, Inventory $inventory): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($inventory->branch_id !== $branch->id, 404);

        $inventory->update($request->validated());

        return response()->json([
            'message' => 'Inventario actualizado exitosamente.',
            'data'    => new InventoryResource($inventory->fresh()->load('product.unidadDeMedida')),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/branches/{branch}/inventory/{inventory}
     *
     * Remove a product from the branch inventory (soft delete).
     * Requires: auth:sanctum + company_admin_or_super.
     */
    public function destroy(Company $company, Branch $branch, Inventory $inventory): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($inventory->branch_id !== $branch->id, 404);

        $inventory->delete();

        return response()->json([
            'message' => 'Producto eliminado del inventario exitosamente.',
        ]);
    }
}
