<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BranchResource;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches
     *
     * List all branches of a company.
     * Requires: auth:sanctum + super_admin role.
     */
    public function index(Company $company): JsonResponse
    {
        $branches = $company->branches()->latest()->get();

        return response()->json([
            'data' => BranchResource::collection($branches),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/branches
     *
     * Create a new branch for a company.
     * Requires: auth:sanctum + super_admin role.
     */
    public function store(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:255'],
            'address'                => ['nullable', 'string', 'max:500'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'state'                  => ['nullable', 'string', 'max:100'],
            'cat_mh_departamento_id' => ['nullable', 'integer', 'exists:cat_mh_departamento,id'],
            'cat_mh_municipio_id'    => ['nullable', 'integer', 'exists:cat_mh_municipio,id'],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'email'                  => ['nullable', 'string', 'email', 'max:255'],
            'latitude'               => ['nullable', 'numeric'],
            'longitude'              => ['nullable', 'numeric'],
            'is_default'             => ['nullable', 'boolean'],
            'status'                 => ['nullable', 'string', 'in:active,inactive'],
        ]);

        // Si se marca como default, desmarcar las demás
        if (! empty($validated['is_default'])) {
            $company->branches()->update(['is_default' => false]);
        }

        $branch = $company->branches()->create(array_merge(
            ['status' => 'active'],
            $validated,
            ['company_id' => $company->id],
        ));

        return response()->json([
            'message' => 'Sucursal creada exitosamente.',
            'branch'  => new BranchResource($branch),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/branches/{branch}
     *
     * Update a branch.
     * Requires: auth:sanctum + super_admin role.
     */
    public function update(Request $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if((int) $branch->company_id !== (int) $company->id, 404);

        $validated = $request->validate([
            'name'                   => ['sometimes', 'required', 'string', 'max:255'],
            'address'                => ['nullable', 'string', 'max:500'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'state'                  => ['nullable', 'string', 'max:100'],
            'cat_mh_departamento_id' => ['nullable', 'integer', 'exists:cat_mh_departamento,id'],
            'cat_mh_municipio_id'    => ['nullable', 'integer', 'exists:cat_mh_municipio,id'],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'email'                  => ['nullable', 'string', 'email', 'max:255'],
            'latitude'               => ['nullable', 'numeric'],
            'longitude'              => ['nullable', 'numeric'],
            'is_default'             => ['nullable', 'boolean'],
            'status'                 => ['nullable', 'string', 'in:active,inactive'],
        ]);

        // Si se marca como default, desmarcar las demás
        if (! empty($validated['is_default'])) {
            $company->branches()
                    ->where('id', '!=', $branch->id)
                    ->update(['is_default' => false]);
        }

        $branch->update($validated);

        return response()->json([
            'message' => 'Sucursal actualizada exitosamente.',
            'branch'  => new BranchResource($branch->fresh()),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/branches/{branch}
     *
     * Delete a branch (cannot delete the default branch).
     * Requires: auth:sanctum + super_admin role.
     */
    public function destroy(Company $company, Branch $branch): JsonResponse
    {
        abort_if((int) $branch->company_id !== (int) $company->id, 404);
        abort_if($branch->is_default, 422, 'No se puede eliminar la sucursal principal.');

        $branch->delete();

        return response()->json(['message' => 'Sucursal eliminada exitosamente.']);
    }
}
