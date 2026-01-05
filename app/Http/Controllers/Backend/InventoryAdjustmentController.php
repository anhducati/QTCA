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
use App\Services\InventoryLogService;

class InventoryAdjustmentController extends Controller
{
    protected string $moduleKey = 'inventory_adjustments';

    protected function authorizeModule(string $action)
    {
        $user = Auth::user();
        if (!$user || !$user->canModule($this->moduleKey, $action)) {
            return redirect()->back()
                ->with('msg-error', 'Bạn không có quyền truy cập chức năng này.');
        }
        return null;
    }

    // =========================================================================
    // INDEX: Danh sách DC
    // =========================================================================
    public function index(Request $req)
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $query = InventoryAdjustment::with(['warehouse', 'stockTake', 'createdBy'])
            ->orderBy('created_at', 'desc');

        if ($req->code)
            $query->where('code', 'like', "%{$req->code}%");

        if ($req->warehouse_id)
            $query->where('warehouse_id', $req->warehouse_id);

        if ($req->from)
            $query->where('adjustment_date', '>=', $req->from);

        if ($req->to)
            $query->where('adjustment_date', '<=', $req->to);

        $adjustments = $query->paginate(20);
        $warehouses = Warehouse::orderBy('name')->get();

        return view('backend.inventory_adjustments.index', compact(
            'adjustments',
            'warehouses'
        ));
    }

    // =========================================================================
    // CREATE (tạo phiếu DC từ kiểm kê hoặc thủ công)
    // =========================================================================
    public function create(Request $req, $stockTakeId = null)
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $stockTake = null;
        if ($stockTakeId) {
            $stockTake = StockTake::with('items.vehicle')->find($stockTakeId);
        }

        $warehouses = Warehouse::orderBy('name')->get();

        return view('backend.inventory_adjustments.create', compact(
            'stockTake',
            'warehouses'
        ));
    }

    // =========================================================================
    // STORE (Tạo phiếu điều chỉnh)
    // =========================================================================
    public function store(Request $req)
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $req->validate([
            'warehouse_id'     => 'required|exists:warehouses,id',
            'adjustment_date'  => 'required|date',
            'items'            => 'required|array|min:1',
            'items.*.frame_no' => 'required',
            'items.*.action'   => 'required|in:add,remove', // add=thêm, remove=thiếu
        ]);

        // Tạo mã DC_x
        $last = InventoryAdjustment::orderBy('id', 'desc')->first();
        $next = $last ? ($last->id + 1) : 1;
        $code = 'DC_' . $next;

        // Tạo phiếu điều chỉnh
        $adj = InventoryAdjustment::create([
            'code'            => $code,
            'warehouse_id'    => $req->warehouse_id,
            'adjustment_date' => $req->adjustment_date,
            'reason'          => $req->reason,
            'stock_take_id'   => $req->stock_take_id,
            'note'            => $req->note,
            'created_by'      => auth()->id(),
        ]);

        // Lặp từng dòng DC
        foreach ($req->items as $i) {

            // Lấy vehicle theo ID hoặc theo số khung
            $vehicle = null;

            if (!empty($i['vehicle_id'])) {
                $vehicle = Vehicle::find($i['vehicle_id']);
            } else {
                $vehicle = Vehicle::where('frame_no', trim($i['frame_no']))->first();
            }

            // Tạo dòng DC
            $item = InventoryAdjustmentItem::create([
                'inventory_adjustment_id' => $adj->id,
                'frame_no'                => trim($i['frame_no']),
                'engine_no'               => $i['engine_no'] ?? null,
                'vehicle_id'              => $vehicle?->id,
                'action'                  => $i['action'],     // add/remove
                'qty'                     => 1,
                'note'                    => $i['note'] ?? null,
            ]);

            // ==========================
            // Xử lý và ghi LOG theo action
            // ==========================

            // 1) Action = add → hệ thống thiếu xe → thêm vào kho
            if ($i['action'] === 'add') {

                // Nếu chưa tồn tại xe, tạo mới
                if (!$vehicle) {
                    $vehicle = Vehicle::create([
                        'frame_no'     => trim($i['frame_no']),
                        'engine_no'    => $i['engine_no'] ?? null,
                        'warehouse_id' => $req->warehouse_id,
                        'status'       => 'in_stock',
                    ]);
                } else {
                    // Nếu xe có nhưng sai kho → chuyển đúng kho
                    $vehicle->warehouse_id = $req->warehouse_id;
                    $vehicle->status = 'in_stock';
                    $vehicle->save();
                }

                // === GHI LOG THÊM XE (add) ===
                InventoryLogService::logAdjustment(
                    $vehicle,
                    $adj,
                    'add',
                    $i['note'] ?? null
                );
            }

            // 2) Action = remove → xe thừa trong hệ thống → loại khỏi kho
            elseif ($i['action'] === 'remove') {

                if ($vehicle) {
                    // Cập nhật trạng thái “mất/thiếu”
                    $vehicle->status = 'missing';
                    $vehicle->save();

                    // === GHI LOG XÓA XE (remove) ===
                    InventoryLogService::logAdjustment(
                        $vehicle,
                        $adj,
                        'remove',
                        $i['note'] ?? null
                    );
                }
            }
        }

        return redirect()
            ->route('admin.inventory_adjustments.show', $adj->id)
            ->with('msg-success', 'Đã tạo phiếu điều chỉnh tồn kho thành công.');
    }

    // =========================================================================
    // SHOW DC_x
    // =========================================================================
    public function show($id)
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $adjustment = InventoryAdjustment::with([
            'warehouse',
            'stockTake',
            'items.vehicle',
            'createdBy'
        ])->findOrFail($id);

        return view('backend.inventory_adjustments.show', compact('adjustment'));
    }
}
