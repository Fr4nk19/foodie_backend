<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Company\StoreCompanyEconomicActivityRequest;
use App\Http\Requests\Api\V1\Company\UpdateCompanyEconomicActivityRequest;
use App\Http\Resources\Api\V1\CompanyEconomicActivityResource;
use App\Models\Company;
use App\Models\EconomicActivityByCompany;
use Illuminate\Http\JsonResponse;

class CompanyEconomicActivityController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/economic-activities
     *
     * List all economic activities assigned to a company.
     * Requires: auth:sanctum + company_admin or super_admin.
     */
    public function index(Company $company): JsonResponse
    {
        $activities = $company->economicActivities()
            ->with('actividadEconomica')
            ->orderByDesc('is_primary')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'data' => CompanyEconomicActivityResource::collection($activities),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/economic-activities
     *
     * Assign an economic activity to a company.
     * Requires: auth:sanctum + company_admin or super_admin.
     */
    public function store(StoreCompanyEconomicActivityRequest $request, Company $company): JsonResponse
    {
        $isPrimary = (bool) ($request->is_primary ?? false);

        // If marking as primary, clear existing primary
        if ($isPrimary) {
            $company->economicActivities()->update(['is_primary' => false]);
        }

        // If this is the first activity, auto-set as primary
        if (! $company->economicActivities()->exists()) {
            $isPrimary = true;
        }

        $activity = EconomicActivityByCompany::create([
            'company_id'         => $company->id,
            'cat_mhactividad_id' => $request->cat_mhactividad_id,
            'is_primary'         => $isPrimary,
        ]);

        return response()->json([
            'message' => 'Actividad económica asignada exitosamente.',
            'data'    => new CompanyEconomicActivityResource($activity->load('actividadEconomica')),
        ], 201);
    }

    /**
     * PATCH /api/v1/companies/{company}/economic-activities/{economicActivity}
     *
     * Update the is_primary flag of a company economic activity.
     * Requires: auth:sanctum + company_admin or super_admin.
     */
    public function update(
        UpdateCompanyEconomicActivityRequest $request,
        Company $company,
        EconomicActivityByCompany $economicActivity
    ): JsonResponse {
        // Ensure the activity belongs to the company
        if ($economicActivity->company_id !== $company->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if ($request->is_primary) {
            $company->economicActivities()->update(['is_primary' => false]);
        }

        $economicActivity->update(['is_primary' => $request->is_primary]);

        return response()->json([
            'message' => 'Actividad económica actualizada exitosamente.',
            'data'    => new CompanyEconomicActivityResource($economicActivity->load('actividadEconomica')),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/economic-activities/{economicActivity}
     *
     * Remove an economic activity from a company.
     * Requires: auth:sanctum + company_admin or super_admin.
     */
    public function destroy(Company $company, EconomicActivityByCompany $economicActivity): JsonResponse
    {
        if ($economicActivity->company_id !== $company->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $economicActivity->delete();

        return response()->json([
            'message' => 'Actividad económica removida exitosamente.',
        ]);
    }
}
