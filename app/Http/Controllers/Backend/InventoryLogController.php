<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryLog::with(['vehicle', 'fromWarehouse', 'toWarehouse', 'creator']);

        if ($frame = $request->get('frame_no')) {
            $query->whereHas('vehicle', function ($q) use ($frame) {
                $q->where('frame_no', 'like', "%{$frame}%");
            });
        }

        if ($type = $request->get('log_type')) {
            $query->where('log_type', $type);
        }

        $logs = $query->orderByDesc('log_date')->orderByDesc('id')->paginate(50);

        return view('backend.inventory_logs.index', compact('logs'));
    }

    public function show(InventoryLog $inventoryLog)
    {
        $inventoryLog->load(['vehicle', 'fromWarehouse', 'toWarehouse', 'creator']);
        return view('backend.inventory_logs.show', compact('inventoryLog'));
    }
}
