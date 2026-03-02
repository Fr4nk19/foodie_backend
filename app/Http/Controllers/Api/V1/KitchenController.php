<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches/{branch}/kitchen/orders
     *
     * Active kitchen orders: pending, preparing, ready.
     * Sorted by priority (urgent → high → normal) then by age (oldest first).
     */
    public function orders(Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $orders = Order::with(['table.tableZone', 'waiter', 'items.product'])
            ->where('branch_id', $branch->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal')")
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'data' => OrderResource::collection($orders),
        ]);
    }

    /**
     * PATCH /api/v1/companies/{company}/branches/{branch}/kitchen/orders/{order}/status
     *
     * Kitchen advances order status: pending → preparing → ready.
     */
    public function updateOrderStatus(Request $request, Company $company, Branch $branch, Order $order): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        $request->validate([
            'status' => ['required', 'in:preparing,ready'],
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Estado de la orden actualizado.',
            'data'    => new OrderResource($order->fresh()->load(['table.tableZone', 'waiter', 'items.product'])),
        ]);
    }

    /**
     * PATCH /api/v1/companies/{company}/branches/{branch}/kitchen/orders/{order}/items/{item}/toggle
     *
     * Toggle the is_done flag on a single order item.
     * When all items are done the order automatically advances to 'ready'.
     */
    public function toggleItem(Request $request, Company $company, Branch $branch, Order $order, int $itemId): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        $item = $order->items()->findOrFail($itemId);
        $item->is_done = ! $item->is_done;
        $item->save();

        // Auto-advance order to 'ready' when all items are done
        $allDone = $order->items()->where('is_done', false)->doesntExist();
        if ($allDone && $order->status === 'preparing') {
            $order->update(['status' => 'ready']);
        }

        return response()->json([
            'message' => 'Ítem actualizado.',
            'data'    => new OrderResource($order->fresh()->load(['table.tableZone', 'waiter', 'items.product'])),
        ]);
    }

    /**
     * GET /api/v1/companies/{company}/branches/{branch}/kitchen/stats
     *
     * Quick stats for the kitchen dashboard header.
     */
    public function stats(Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $counts = Order::where('branch_id', $branch->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'data' => [
                'pending'      => (int) ($counts['pending']   ?? 0),
                'preparing'    => (int) ($counts['preparing'] ?? 0),
                'ready'        => (int) ($counts['ready']     ?? 0),
                'total_active' => (int) $counts->sum(),
            ],
        ]);
    }
}
