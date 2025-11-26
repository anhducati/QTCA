<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\StockTake;
use App\Models\Warehouse;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryAdjustment::with('warehouse');

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        $adjustments = $query->orderByDesc('adjustment_date')->orderByDesc('id')->paginate(20);

        return view('backend.inventory_adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $stockTakes = StockTake::orderByDesc('stock_take_date')->get();

        return view('backend.inventory_adjustments.create', compact('warehouses', 'stockTakes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:50|unique:inventory_adjustments,code',
            'adjustment_date' => 'required|date',
            'warehouse_id'    => 'required|exists:warehouses,id',
            'reason'          => 'nullable|string|max:255',
            'stock_take_id'   => 'nullable|exists:stock_takes,id',
            'note'            => 'nullable|string',

            'items'                   => 'nullable|array',
            'items.*.vehicle_id'      => 'nullable|exists:vehicles,id',
            'items.*.frame_no'        => 'nullable|string|max:100',
            'items.*.engine_no'       => 'nullable|string|max:100',
            'items.*.action'          => 'required|string|max:20',
            'items.*.qty'             => 'nullable|integer|min:1',
            'items.*.note'            => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $userId = Auth::id();

            $adj = InventoryAdjustment::create([
                'code'            => $data['code'],
                'adjustment_date' => $data['adjustment_date'],
                'warehouse_id'    => $data['warehouse_id'],
                'reason'          => $data['reason'] ?? null,
                'stock_take_id'   => $data['stock_take_id'] ?? null,
                'note'            => $data['note'] ?? null,
                'created_by'      => $userId,
                'approved_by'     => null,
            ]);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $qty = $item['qty'] ?? 1;

                    InventoryAdjustmentItem::create([
                        'inventory_adjustment_id' => $adj->id,
                        'vehicle_id'              => $item['vehicle_id'] ?? null,
                        'frame_no'                => $item['frame_no'] ?? null,
                        'engine_no'               => $item['engine_no'] ?? null,
                        'action'                  => $item['action'],
                        'qty'                     => $qty,
                        'note'                    => $item['note'] ?? null,
                    ]);

                    // Nếu cần xử lý thay đổi Vehicle/status thì viết thêm logic ở đây
                }
            }
        });

        return redirect()->route('admin.inventory_adjustments.index')
            ->with('success', 'Tạo phiếu điều chỉnh tồn kho thành công');
    }

    public function show(InventoryAdjustment $inventoryAdjustment)
    {
        $inventoryAdjustment->load(['warehouse', 'stockTake', 'items.vehicle']);
        return view('backend.inventory_adjustments.show', compact('inventoryAdjustment'));
    }

    public function edit(InventoryAdjustment $inventoryAdjustment)
    {
        $inventoryAdjustment->load('items');
        $warehouses = Warehouse::orderBy('name')->get();
        $stockTakes = StockTake::orderByDesc('stock_take_date')->get();

        return view('backend.inventory_adjustments.edit', compact('inventoryAdjustment', 'warehouses', 'stockTakes'));
    }

    public function update(Request $request, InventoryAdjustment $inventoryAdjustment)
    {
        $data = $request->validate([
            'adjustment_date' => 'required|date',
            'warehouse_id'    => 'required|exists:warehouses,id',
            'reason'          => 'nullable|string|max:255',
            'stock_take_id'   => 'nullable|exists:stock_takes,id',
            'note'            => 'nullable|string',

            'items'                   => 'nullable|array',
            'items.*.id'              => 'nullable|exists:inventory_adjustment_items,id',
            'items.*.vehicle_id'      => 'nullable|exists:vehicles,id',
            'items.*.frame_no'        => 'nullable|string|max:100',
            'items.*.engine_no'       => 'nullable|string|max:100',
            'items.*.action'          => 'required|string|max:20',
            'items.*.qty'             => 'nullable|integer|min:1',
            'items.*.note'            => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $inventoryAdjustment) {
            $inventoryAdjustment->update([
                'adjustment_date' => $data['adjustment_date'],
                'warehouse_id'    => $data['warehouse_id'],
                'reason'          => $data['reason'] ?? null,
                'stock_take_id'   => $data['stock_take_id'] ?? null,
                'note'            => $data['note'] ?? null,
            ]);

            if (!empty($data['items'])) {
                $keepIds = [];

                foreach ($data['items'] as $item) {
                    $qty = $item['qty'] ?? 1;

                    if (!empty($item['id'])) {
                        $detail = InventoryAdjustmentItem::where('inventory_adjustment_id', $inventoryAdjustment->id)
                            ->where('id', $item['id'])
                            ->first();
                        if ($detail) {
                            $detail->update([
                                'vehicle_id' => $item['vehicle_id'] ?? null,
                                'frame_no'   => $item['frame_no'] ?? null,
                                'engine_no'  => $item['engine_no'] ?? null,
                                'action'     => $item['action'],
                                'qty'        => $qty,
                                'note'       => $item['note'] ?? null,
                            ]);
                            $keepIds[] = $detail->id;
                        }
                    } else {
                        $detail = InventoryAdjustmentItem::create([
                            'inventory_adjustment_id' => $inventoryAdjustment->id,
                            'vehicle_id'              => $item['vehicle_id'] ?? null,
                            'frame_no'                => $item['frame_no'] ?? null,
                            'engine_no'               => $item['engine_no'] ?? null,
                            'action'                  => $item['action'],
                            'qty'                     => $qty,
                            'note'                    => $item['note'] ?? null,
                        ]);
                        $keepIds[] = $detail->id;
                    }

                    // Xử lý thực tế (tăng/giảm tồn) có thể viết thêm ở đây
                }

                InventoryAdjustmentItem::where('inventory_adjustment_id', $inventoryAdjustment->id)
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }
        });

        return redirect()->route('admin.inventory_adjustments.index')
            ->with('success', 'Cập nhật phiếu điều chỉnh tồn kho thành công');
    }

    public function destroy(InventoryAdjustment $inventoryAdjustment)
    {
        $inventoryAdjustment->delete();

        return redirect()->route('admin.inventory_adjustments.index')
            ->with('success', 'Xóa phiếu điều chỉnh tồn kho thành công');
    }
}
