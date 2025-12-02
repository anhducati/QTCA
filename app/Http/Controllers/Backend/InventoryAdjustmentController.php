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

    // ===== INDEX: Danh sách DC =====
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

    // ===== TẠO MỚI (thường được tạo từ phiếu kiểm kê) =====
    public function create(Request $req, $stockTakeId = null)
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $stockTake = null;
        if ($stockTakeId)
            $stockTake = StockTake::with('items.vehicle')->find($stockTakeId);

        $warehouses = Warehouse::orderBy('name')->get();

        return view('backend.inventory_adjustments.create', compact(
            'stockTake',
            'warehouses'
        ));
    }

    // ===== STORE =====
    public function store(Request $req)
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $req->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'adjustment_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.frame_no' => 'required',
            'items.*.action' => 'required|in:add,remove'
        ]);

        // Sinh mã DC_x
        $last = InventoryAdjustment::orderBy('id', 'desc')->first();
        $next = $last ? ($last->id + 1) : 1;
        $code = 'DC_' . $next;

        $adj = InventoryAdjustment::create([
            'code' => $code,
            'warehouse_id' => $req->warehouse_id,
            'adjustment_date' => $req->adjustment_date,
            'reason' => $req->reason,
            'stock_take_id' => $req->stock_take_id,
            'note' => $req->note,
            'created_by' => auth()->id(),
        ]);

        foreach ($req->items as $i) {
            InventoryAdjustmentItem::create([
                'inventory_adjustment_id' => $adj->id,
                'frame_no' => $i['frame_no'],
                'engine_no' => $i['engine_no'] ?? null,
                'vehicle_id' => $i['vehicle_id'] ?? null,
                'action' => $i['action'],
                'note' => $i['note'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.inventory_adjustments.show', $adj->id)
            ->with('msg-success', 'Đã tạo phiếu điều chỉnh thành công.');
    }

    // ===== SHOW DC_x =====
    public function show($id)
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $adjustment = InventoryAdjustment::with([
            'warehouse',
            'stockTake',
            'items.vehicle',
        ])->findOrFail($id);

        return view(
            'backend.inventory_adjustments.show',
            compact('adjustment')
        );
    }
}
