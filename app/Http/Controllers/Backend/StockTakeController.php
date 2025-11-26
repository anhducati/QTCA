<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\Warehouse;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTakeController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTake::with('warehouse');

        if ($code = $request->get('code')) {
            $query->where('code', 'like', "%{$code}%");
        }

        $stockTakes = $query->orderByDesc('stock_take_date')->orderByDesc('id')->paginate(20);

        return view('backend.stock_takes.index', compact('stockTakes'));
    }

    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        return view('backend.stock_takes.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'          => 'required|string|max:50|unique:stock_takes,code',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'stock_take_date'=> 'required|date',
            'note'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $userId = Auth::id();

            StockTake::create([
                'code'           => $data['code'],
                'warehouse_id'   => $data['warehouse_id'],
                'stock_take_date'=> $data['stock_take_date'],
                'status'         => 'draft',
                'note'           => $data['note'] ?? null,
                'created_by'     => $userId,
                'approved_by'    => null,
            ]);
        });

        return redirect()->route('admin.stock_takes.index')
            ->with('success', 'Tạo phiếu kiểm kê thành công');
    }

    public function show(StockTake $stockTake)
    {
        $stockTake->load(['warehouse', 'items.vehicle']);
        return view('backend.stock_takes.show', compact('stockTake'));
    }

    public function edit(StockTake $stockTake)
    {
        $stockTake->load('items.vehicle');
        $warehouses = Warehouse::orderBy('name')->get();
        return view('backend.stock_takes.edit', compact('stockTake', 'warehouses'));
    }

    // cập nhật chung + danh sách chi tiết
    public function update(Request $request, StockTake $stockTake)
    {
        $data = $request->validate([
            'warehouse_id'    => 'required|exists:warehouses,id',
            'stock_take_date' => 'required|date',
            'status'          => 'nullable|string|max:20',
            'note'            => 'nullable|string',

            'items'                      => 'nullable|array',
            'items.*.id'                 => 'nullable|exists:stock_take_items,id',
            'items.*.vehicle_id'         => 'nullable|exists:vehicles,id',
            'items.*.frame_no'           => 'nullable|string|max:100',
            'items.*.engine_no'          => 'nullable|string|max:100',
            'items.*.system_exists'      => 'nullable|boolean',
            'items.*.is_present'         => 'nullable|boolean',
            'items.*.note'               => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $stockTake) {
            $stockTake->update([
                'warehouse_id'    => $data['warehouse_id'],
                'stock_take_date' => $data['stock_take_date'],
                'status'          => $data['status'] ?? $stockTake->status,
                'note'            => $data['note'] ?? null,
            ]);

            if (!empty($data['items'])) {
                $keepIds = [];

                foreach ($data['items'] as $item) {
                    $systemExists = isset($item['system_exists']) ? (int) $item['system_exists'] : 1;
                    $isPresent    = isset($item['is_present']) ? (int) $item['is_present'] : 1;

                    if (!empty($item['id'])) {
                        $detail = StockTakeItem::where('stock_take_id', $stockTake->id)
                            ->where('id', $item['id'])
                            ->first();
                        if ($detail) {
                            $detail->update([
                                'vehicle_id'    => $item['vehicle_id'] ?? null,
                                'frame_no'      => $item['frame_no'] ?? null,
                                'engine_no'     => $item['engine_no'] ?? null,
                                'system_exists' => $systemExists,
                                'is_present'    => $isPresent,
                                'note'          => $item['note'] ?? null,
                            ]);
                            $keepIds[] = $detail->id;
                        }
                    } else {
                        $detail = StockTakeItem::create([
                            'stock_take_id'  => $stockTake->id,
                            'vehicle_id'     => $item['vehicle_id'] ?? null,
                            'frame_no'       => $item['frame_no'] ?? null,
                            'engine_no'      => $item['engine_no'] ?? null,
                            'system_exists'  => $systemExists,
                            'is_present'     => $isPresent,
                            'note'           => $item['note'] ?? null,
                        ]);
                        $keepIds[] = $detail->id;
                    }
                }

                StockTakeItem::where('stock_take_id', $stockTake->id)
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }
        });

        return redirect()->route('admin.stock_takes.index')
            ->with('success', 'Cập nhật phiếu kiểm kê thành công');
    }

    public function destroy(StockTake $stockTake)
    {
        $stockTake->delete();

        return redirect()->route('admin.stock_takes.index')
            ->with('success', 'Xóa phiếu kiểm kê thành công');
    }
}
