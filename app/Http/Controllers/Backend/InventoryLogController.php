<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryLogController extends Controller
{
    protected string $moduleKey = 'inventory_logs';

    protected function authorizeModule(string $action)
    {
        $user = Auth::user();
        if (!$user || !$user->canModule($this->moduleKey, $action)) {
            return redirect()->back()
                ->with('msg-error', 'Bạn không có quyền truy cập chức năng này.');
        }
        return null;
    }

    /**
     * Danh sách nhật ký tồn kho
     * Route: GET /admin/nhat-ky-kho  -> name: admin.inventory_logs.index
     */
    public function index(Request $request)
    {
        if ($resp = $this->authorizeModule('read')) {
            return $resp;
        }

        $query = InventoryLog::with([
                'vehicle.model.brand',
                'fromWarehouse',
                'toWarehouse',
                'creator',
            ])
            ->orderBy('log_date', 'desc');

        // Lọc theo kho (từ hoặc đến)
        if ($request->filled('warehouse_id')) {
            $wid = $request->warehouse_id;
            $query->where(function ($q) use ($wid) {
                $q->where('from_warehouse_id', $wid)
                  ->orWhere('to_warehouse_id', $wid);
            });
        }

        // Lọc theo loại log: import / export / retail_sale / adjustment ...
        if ($request->filled('log_type')) {
            $query->where('log_type', $request->log_type);
        }

        // Lọc theo mã số khung
        if ($request->filled('frame_no')) {
            $frame = trim($request->frame_no);
            $query->whereHas('vehicle', function ($q) use ($frame) {
                $q->where('frame_no', 'like', "%{$frame}%");
            });
        }

        // Lọc theo ngày
        if ($request->filled('from')) {
            $query->whereDate('log_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('log_date', '<=', $request->to);
        }

        // Lọc theo bảng tham chiếu (import_receipts / export_receipts / vehicle_sales / inventory_adjustments)
        if ($request->filled('ref_table')) {
            $query->where('ref_table', $request->ref_table);
        }

        $logs = $query->paginate(50);
        $warehouses = Warehouse::orderBy('name')->get();

        return view('backend.inventory_logs.index', compact('logs', 'warehouses'));
    }
}
