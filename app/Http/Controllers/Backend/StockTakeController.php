<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use App\Models\InventoryLog;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockTakeController extends Controller
{
    protected string $moduleKey = 'stock_takes';

    protected function authorizeModule(string $action)
    {
        $user = Auth::user();
        if (!$user || !$user->canModule($this->moduleKey, $action)) {
            return redirect()->back()
                ->with('msg-error', 'Tài khoản của bạn không có quyền với chức năng kiểm kê.');
        }
        return null;
    }

    public function index()
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $stockTakes = StockTake::with(['warehouse', 'creator'])
            ->orderByDesc('stock_take_date')
            ->orderByDesc('id')
            ->paginate(20);

        return view('backend.stock_takes.index', compact('stockTakes'));
    }

    public function create()
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $warehouses = Warehouse::orderBy('name')->get();

        return view('backend.stock_takes.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        if ($resp = $this->authorizeModule('create')) return $resp;

        $data = $request->validate([
            'warehouse_id'    => 'required|exists:warehouses,id',
            'stock_take_date' => 'required|date',
            'note'            => 'nullable|string',
        ], [
            'warehouse_id.required' => 'Vui lòng chọn kho cần kiểm kê.',
            'stock_take_date.required' => 'Vui lòng chọn ngày kiểm kê.',
        ]);

        // Sinh mã KK_1, KK_2 ...
        $last = StockTake::where('code', 'like', 'KK_%')
            ->orderByRaw("CAST(SUBSTRING(code, 4) AS UNSIGNED) DESC")
            ->first();

        if ($last) {
            $lastNumber = (int) str_replace('KK_', '', $last->code);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        $code = 'KK_' . $nextNumber;

        DB::beginTransaction();

        try {
            $stockTake = StockTake::create([
                'code'           => $code,
                'warehouse_id'   => $data['warehouse_id'],
                'stock_take_date'=> $data['stock_take_date'],
                'status'         => StockTake::STATUS_DRAFT,
                'note'           => $data['note'] ?? null,
                'created_by'     => Auth::id(),
            ]);

            // Tạo list item từ danh sách xe đang trong kho
            $vehicles = Vehicle::where('warehouse_id', $data['warehouse_id'])
                ->whereIn('status', ['in_stock', 'demo', 'demo_out']) // tuỳ anh
                ->orderBy('model_id')
                ->orderBy('frame_no')
                ->get();

            foreach ($vehicles as $v) {
                StockTakeItem::create([
                    'stock_take_id' => $stockTake->id,
                    'vehicle_id'    => $v->id,
                    'frame_no'      => $v->frame_no,
                    'engine_no'     => $v->engine_no,
                    'system_exists' => 1,
                    'is_present'    => 1,   // mặc định là "có"
                    'note'          => null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.stock_takes.show', $stockTake->id)
                ->with('msg-success', 'Đã tạo phiếu kiểm kê ' . $stockTake->code . '. Vui lòng rà soát lại danh sách xe.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('msg-error', 'Lỗi khi tạo phiếu kiểm kê: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        if ($resp = $this->authorizeModule('read')) return $resp;

        $stockTake = StockTake::with([
            'warehouse',
            'creator',
            'items.vehicle.model',
        ])->findOrFail($id);

        return view('backend.stock_takes.show', compact('stockTake'));
    }

    /**
     * Cập nhật tick có mặt / không có mặt + thêm xe lạ (nếu anh muốn)
     */
    public function updateItems(Request $request, $id)
    {
        if ($resp = $this->authorizeModule('update')) return $resp;

        $stockTake = StockTake::with('items')->findOrFail($id);

        if ($stockTake->status !== StockTake::STATUS_DRAFT) {
            return redirect()->back()
                ->with('msg-error', 'Phiếu kiểm kê đã được xác nhận, không thể sửa.');
        }

        $data = $request->validate([
            'items'                      => 'nullable|array',
            'items.*.id'                => 'required|exists:stock_take_items,id',
            'items.*.is_present'        => 'nullable|boolean',
            'items.*.note'              => 'nullable|string',
            // Xe lạ (optional)
            'new_items'                 => 'nullable|array',
            'new_items.*.frame_no'      => 'nullable|string|max:100',
            'new_items.*.engine_no'     => 'nullable|string|max:100',
            'new_items.*.note'          => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật các item có sẵn
            if (!empty($data['items'])) {
                foreach ($data['items'] as $it) {
                    /** @var StockTakeItem $item */
                    $item = $stockTake->items->firstWhere('id', $it['id']);
                    if (!$item) continue;

                    $item->is_present = !empty($it['is_present']) ? 1 : 0;
                    $item->note       = $it['note'] ?? null;
                    $item->save();
                }
            }

            // Thêm xe lạ (không có trong hệ thống)
            if (!empty($data['new_items'])) {
                foreach ($data['new_items'] as $ni) {
                    if (empty($ni['frame_no']) && empty($ni['engine_no'])) {
                        continue;
                    }
                    StockTakeItem::create([
                        'stock_take_id' => $stockTake->id,
                        'vehicle_id'    => null,
                        'frame_no'      => $ni['frame_no'] ?? null,
                        'engine_no'     => $ni['engine_no'] ?? null,
                        'system_exists' => 0,
                        'is_present'    => 1,
                        'note'          => $ni['note'] ?? 'Xe lạ, không có trong hệ thống.',
                    ]);
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.stock_takes.show', $stockTake->id)
                ->with('msg-success', 'Đã cập nhật danh sách kiểm kê.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('msg-error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    /**
     * XÁC NHẬN KIỂM KÊ & TỰ ĐỘNG TẠO PHIẾU ĐIỀU CHỈNH
     */
    public function confirm(Request $request, $id)
    {
        if ($resp = $this->authorizeModule('update')) return $resp;

        $stockTake = StockTake::with(['items', 'warehouse'])->findOrFail($id);

        if ($stockTake->status !== StockTake::STATUS_DRAFT) {
            return redirect()->back()
                ->with('msg-error', 'Phiếu kiểm kê đã được xác nhận trước đó.');
        }

        DB::beginTransaction();

        try {
            // 1. Sinh mã phiếu điều chỉnh: DC_1, DC_2 ...
            $lastAdj = InventoryAdjustment::where('code', 'like', 'DC_%')
                ->orderByRaw("CAST(SUBSTRING(code, 4) AS UNSIGNED) DESC")
                ->first();

            if ($lastAdj) {
                $lastNumber = (int) str_replace('DC_', '', $lastAdj->code);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            $adjCode = 'DC_' . $nextNumber;

            $today = Carbon::now()->toDateString();

            $adjustment = InventoryAdjustment::create([
                'code'            => $adjCode,
                'adjustment_date' => $today,
                'warehouse_id'    => $stockTake->warehouse_id,
                'reason'          => 'Điều chỉnh theo phiếu kiểm kê ' . $stockTake->code,
                'stock_take_id'   => $stockTake->id,
                'note'            => $stockTake->note,
                'created_by'      => Auth::id(),
            ]);

            $logUserId = Auth::id();

            // 2. Duyệt qua từng item để tạo điều chỉnh + log
            foreach ($stockTake->items as $item) {
                // Xe trong hệ thống nhưng KHÔNG có mặt → mất xe
                if ($item->system_exists && !$item->is_present && $item->vehicle_id) {
                    $vehicle = Vehicle::find($item->vehicle_id);

                    InventoryAdjustmentItem::create([
                        'inventory_adjustment_id' => $adjustment->id,
                        'vehicle_id'              => $item->vehicle_id,
                        'frame_no'                => $item->frame_no,
                        'engine_no'               => $item->engine_no,
                        'action'                  => 'loss', // quy ước: loss = thiếu
                        'qty'                     => 1,
                        'note'                    => $item->note,
                    ]);

                    // Cập nhật trạng thái xe (tuỳ anh đặt: lost / missing / in_stock ...)
                    if ($vehicle) {
                        $vehicle->status = 'lost';
                        $vehicle->save();

                        InventoryLog::create([
                            'vehicle_id'        => $vehicle->id,
                            'log_type'          => 'adjust',
                            'ref_table'         => 'inventory_adjustments',
                            'ref_id'            => $adjustment->id,
                            'from_warehouse_id' => $vehicle->warehouse_id,
                            'to_warehouse_id'   => null,
                            'log_date'          => Carbon::now(),
                            'note'              => 'Điều chỉnh thiếu xe theo KK ' . $stockTake->code,
                            'created_by'        => $logUserId,
                        ]);
                    }

                // Xe không có trong hệ thống nhưng CÓ mặt → xe thừa / xe lạ
                } elseif (!$item->system_exists && $item->is_present) {
                    InventoryAdjustmentItem::create([
                        'inventory_adjustment_id' => $adjustment->id,
                        'vehicle_id'              => null,
                        'frame_no'                => $item->frame_no,
                        'engine_no'               => $item->engine_no,
                        'action'                  => 'found', // quy ước: found = thừa
                        'qty'                     => 1,
                        'note'                    => $item->note ?: 'Xe lạ ngoài hệ thống.',
                    ]);
                }
            }

            // 3. Cập nhật trạng thái phiếu kiểm kê
            $stockTake->status      = StockTake::STATUS_CONFIRMED;
            $stockTake->approved_by = Auth::id();
            $stockTake->save();

            DB::commit();

            return redirect()
                ->route('admin.inventory_adjustments.show', $adjustment->id)
                ->with('msg-success', 'Đã xác nhận kiểm kê và tạo phiếu điều chỉnh ' . $adjustment->code);
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('msg-error', 'Lỗi khi xác nhận kiểm kê: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($resp = $this->authorizeModule('delete')) return $resp;

        $stockTake = StockTake::findOrFail($id);

        if ($stockTake->status !== StockTake::STATUS_DRAFT) {
            return redirect()->back()
                ->with('msg-error', 'Chỉ được xóa phiếu kiểm kê đang ở trạng thái nháp.');
        }

        $stockTake->delete();

        return redirect()
            ->route('admin.stock_takes.index')
            ->with('msg-success', 'Đã xóa phiếu kiểm kê.');
    }
}
