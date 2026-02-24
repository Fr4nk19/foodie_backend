<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches/{branch}/dashboard
     *
     * Returns summary stats for the branch dashboard.
     */
    public function stats(Request $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $today = now()->toDateString();

        $pendingOrders = Order::where('branch_id', $branch->id)
            ->where('status', 'pending')
            ->whereDate('created_at', $today)
            ->count();

        $inProgressOrders = Order::where('branch_id', $branch->id)
            ->where('status', 'preparing')
            ->whereDate('created_at', $today)
            ->count();

        $completedToday = Order::where('branch_id', $branch->id)
            ->where('status', 'delivered')
            ->whereDate('created_at', $today)
            ->count();

        $totalTables = Table::where('branch_id', $branch->id)->count();
        $activeTables = Table::where('branch_id', $branch->id)
            ->where('status', 'occupied')
            ->count();

        $staff = User::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->count();

        // Recent orders (last 10)
        $recentOrders = Order::with(['table', 'waiter', 'items.product'])
            ->where('branch_id', $branch->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'data' => [
                'stats' => [
                    'pending_orders'     => $pendingOrders,
                    'in_progress_orders' => $inProgressOrders,
                    'completed_today'    => $completedToday,
                    'active_tables'      => $activeTables,
                    'total_tables'       => $totalTables,
                    'staff'              => $staff,
                ],
                'recent_orders' => OrderResource::collection($recentOrders),
            ],
        ]);
    }
}
