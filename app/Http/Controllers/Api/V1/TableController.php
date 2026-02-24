<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Table\StoreTableRequest;
use App\Http\Requests\Api\V1\Table\UpdateTableRequest;
use App\Http\Resources\Api\V1\TableResource;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches/{branch}/tables
     */
    public function index(Request $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $query = Table::with(['activeOrder.items'])
            ->where('branch_id', $branch->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->orderBy('zone')->orderBy('number')->get();

        return response()->json([
            'data' => TableResource::collection($tables),
        ]);
    }

    /**
     * GET /api/v1/companies/{company}/branches/{branch}/tables/{table}
     */
    public function show(Company $company, Branch $branch, Table $table): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($table->branch_id !== $branch->id, 404);

        return response()->json([
            'data' => new TableResource($table->load(['activeOrder.items'])),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/branches/{branch}/tables
     */
    public function store(StoreTableRequest $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $table = Table::create(array_merge(
            $request->validated(),
            ['branch_id' => $branch->id]
        ));

        return response()->json([
            'message' => 'Mesa creada exitosamente.',
            'data'    => new TableResource($table),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/branches/{branch}/tables/{table}
     */
    public function update(UpdateTableRequest $request, Company $company, Branch $branch, Table $table): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($table->branch_id !== $branch->id, 404);

        $table->update($request->validated());

        return response()->json([
            'message' => 'Mesa actualizada exitosamente.',
            'data'    => new TableResource($table->fresh()->load(['activeOrder.items'])),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/branches/{branch}/tables/{table}
     */
    public function destroy(Company $company, Branch $branch, Table $table): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($table->branch_id !== $branch->id, 404);

        $table->delete();

        return response()->json([
            'message' => 'Mesa eliminada exitosamente.',
        ]);
    }
}
