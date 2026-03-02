<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TableZoneResource;
use App\Models\Branch;
use App\Models\Company;
use App\Models\TableZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableZoneController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches/{branch}/zones
     *
     * List all zones of a branch, including table count.
     */
    public function index(Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $zones = $branch->tableZones()
                        ->withCount('tables')
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();

        return response()->json([
            'data' => TableZoneResource::collection($zones),
        ]);
    }

    /**
     * GET /api/v1/companies/{company}/branches/{branch}/zones/{zone}
     *
     * Show a zone with its tables (including each table's active order).
     */
    public function show(Company $company, Branch $branch, TableZone $zone): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($zone->branch_id !== $branch->id, 404);

        $zone->load(['tables' => fn ($q) => $q->with('activeOrder')->orderBy('number')]);

        return response()->json([
            'data' => new TableZoneResource($zone),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/branches/{branch}/zones
     *
     * Create a new zone for the branch.
     */
    public function store(Request $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'status'      => ['nullable', 'in:active,inactive'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $zone = $branch->tableZones()->create($validated);

        return response()->json([
            'message' => 'Zona creada exitosamente.',
            'data'    => new TableZoneResource($zone),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/branches/{branch}/zones/{zone}
     *
     * Update a zone.
     */
    public function update(Request $request, Company $company, Branch $branch, TableZone $zone): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($zone->branch_id !== $branch->id, 404);

        $validated = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'status'      => ['nullable', 'in:active,inactive'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $zone->update($validated);

        return response()->json([
            'message' => 'Zona actualizada exitosamente.',
            'data'    => new TableZoneResource($zone->fresh()),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/branches/{branch}/zones/{zone}
     *
     * Delete a zone. Fails if it still has tables assigned.
     */
    public function destroy(Company $company, Branch $branch, TableZone $zone): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($zone->branch_id !== $branch->id, 404);
        abort_if($zone->tables()->exists(), 422, 'No se puede eliminar una zona que tiene mesas asignadas.');

        $zone->delete();

        return response()->json(['message' => 'Zona eliminada exitosamente.']);
    }
}
