<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Order\StoreOrderRequest;
use App\Http\Requests\Api\V1\Order\UpdateOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * GET /api/v1/companies/{company}/branches/{branch}/orders
     */
    public function index(Request $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        $query = Order::with(['table', 'waiter', 'items.product'])
            ->where('branch_id', $branch->id);

        if ($request->filled('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        if ($request->filled('waiter_id')) {
            $query->where('waiter_id', $request->waiter_id);
        }

        if ($request->filled('today') && $request->today) {
            $query->whereDate('created_at', now()->toDateString());
        }

        $paginator = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => OrderResource::collection($paginator->items()),
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
     * GET /api/v1/companies/{company}/branches/{branch}/orders/{order}
     */
    public function show(Company $company, Branch $branch, Order $order): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        return response()->json([
            'data' => new OrderResource($order->load(['table', 'waiter', 'items.product'])),
        ]);
    }

    /**
     * POST /api/v1/companies/{company}/branches/{branch}/orders
     */
    public function store(StoreOrderRequest $request, Company $company, Branch $branch): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);

        $order = DB::transaction(function () use ($request, $branch) {
            $order = Order::create([
                'branch_id' => $branch->id,
                'table_id'  => $request->table_id,
                'waiter_id' => $request->waiter_id ?? $request->user()?->id,
                'type'      => $request->input('type', 'dine_in'),
                'priority'  => $request->input('priority', 'normal'),
                'notes'     => $request->notes,
                'status'    => 'pending',
                'total'     => 0,
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $unitPrice = $product->precio;
                $subtotal  = $unitPrice * $item['quantity'];
                $total    += $subtotal;

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal'   => $subtotal,
                    'notes'      => $item['notes'] ?? null,
                ]);
            }

            $order->update(['total' => $total]);

            // Mark table as occupied if table_id was provided
            if ($request->table_id) {
                Table::where('id', $request->table_id)->update(['status' => 'occupied']);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Pedido creado exitosamente.',
            'data'    => new OrderResource($order->load(['table', 'waiter', 'items.product'])),
        ], 201);
    }

    /**
     * PUT /api/v1/companies/{company}/branches/{branch}/orders/{order}
     */
    public function update(UpdateOrderRequest $request, Company $company, Branch $branch, Order $order): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        $order->update($request->validated());

        return response()->json([
            'message' => 'Pedido actualizado exitosamente.',
            'data'    => new OrderResource($order->fresh()->load(['table', 'waiter', 'items.product'])),
        ]);
    }

    /**
     * PATCH /api/v1/companies/{company}/branches/{branch}/orders/{order}/status
     */
    public function updateStatus(Request $request, Company $company, Branch $branch, Order $order): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        $request->validate([
            'status' => ['required', 'string', 'in:pending,preparing,ready,delivered,cancelled'],
        ]);

        $newStatus = $request->status;

        if ($newStatus === 'cancelled') {
            $request->validate([
                'reason' => ['nullable', 'string', 'max:500'],
            ]);
            $order->cancel_reason = $request->reason;
        }

        $order->status = $newStatus;
        $order->save();

        // When order is delivered or cancelled, free the table
        if (in_array($newStatus, ['delivered', 'cancelled']) && $order->table_id) {
            $hasOtherActiveOrders = Order::where('table_id', $order->table_id)
                ->where('id', '!=', $order->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->exists();

            if (!$hasOtherActiveOrders) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }

        return response()->json([
            'message' => 'Estado del pedido actualizado.',
            'data'    => new OrderResource($order->fresh()->load(['table', 'waiter', 'items.product'])),
        ]);
    }

    /**
     * PATCH /api/v1/companies/{company}/branches/{branch}/orders/{order}/items/{item}/toggle
     */
    public function toggleItem(Request $request, Company $company, Branch $branch, Order $order, int $itemId): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        $item = $order->items()->findOrFail($itemId);
        $item->is_done = !$item->is_done;
        $item->save();

        return response()->json([
            'message' => 'Ítem actualizado.',
            'data'    => new OrderResource($order->fresh()->load(['table', 'waiter', 'items.product'])),
        ]);
    }

    /**
     * DELETE /api/v1/companies/{company}/branches/{branch}/orders/{order}
     */
    public function destroy(Request $request, Company $company, Branch $branch, Order $order): JsonResponse
    {
        abort_if($branch->company_id !== $company->id, 404);
        abort_if($order->branch_id !== $branch->id, 404);

        // Free the table if needed
        if ($order->table_id) {
            $hasOtherActiveOrders = Order::where('table_id', $order->table_id)
                ->where('id', '!=', $order->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->exists();

            if (!$hasOtherActiveOrders) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        }

        $order->delete();

        return response()->json([
            'message' => 'Pedido eliminado exitosamente.',
        ]);
    }
}
