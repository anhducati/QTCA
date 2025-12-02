<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Vehicle;
use App\Models\ImportReceipt;
use App\Models\ExportReceipt;
use App\Models\VehicleSale;
use App\Models\InventoryAdjustment;
use Illuminate\Support\Facades\Auth;

class InventoryLogService
{
    /**
     * Hàm ghi log cơ bản
     */
    protected static function write(array $data): InventoryLog
    {
        $log = new InventoryLog();
        $log->vehicle_id        = $data['vehicle_id'];
        $log->log_type          = $data['log_type'];        // import/export/transfer/sale/demo/adjustment
        $log->ref_table         = $data['ref_table'] ?? null;
        $log->ref_id            = $data['ref_id'] ?? null;
        $log->from_warehouse_id = $data['from_warehouse_id'] ?? null;
        $log->to_warehouse_id   = $data['to_warehouse_id'] ?? null;
        $log->log_date          = $data['log_date'] ?? now();
        $log->note              = $data['note'] ?? null;
        $log->created_by        = Auth::id() ?? $data['created_by'] ?? null;
        $log->save();

        return $log;
    }

    /**
     * Nhập kho (từ phiếu nhập)
     */
    public static function import(Vehicle $vehicle, ImportReceipt $receipt, ?string $note = null): InventoryLog
    {
        return self::write([
            'vehicle_id'        => $vehicle->id,
            'log_type'          => 'import',
            'ref_table'         => 'import_receipts',
            'ref_id'            => $receipt->id,
            'from_warehouse_id' => null,
            'to_warehouse_id'   => $receipt->warehouse_id,
            'log_date'          => now(),
            'note'              => $note ?? ('Nhập kho từ phiếu ' . $receipt->code),
        ]);
    }

    /**
     * Xuất kho / chuyển kho / demo (từ phiếu xuất)
     */
    public static function export(Vehicle $vehicle, ExportReceipt $receipt, ?string $note = null): InventoryLog
    {
        // map export_type -> log_type
        $type = $receipt->export_type;

        if ($type === 'transfer') {
            $logType = 'transfer';
        } elseif ($type === 'demo') {
            $logType = 'demo';
        } else {
            // sell hoặc khác -> coi là export
            $logType = 'export';
        }

        return self::write([
            'vehicle_id'        => $vehicle->id,
            'log_type'          => $logType,
            'ref_table'         => 'export_receipts',
            'ref_id'            => $receipt->id,
            'from_warehouse_id' => $receipt->warehouse_id,
            // Nếu anh có cột "warehouse_to_id" trong export_receipts thì truyền vào, không thì để null
            'to_warehouse_id'   => $receipt->warehouse_id, // hoặc null nếu không chuyển kho
            'log_date'          => now(),
            'note'              => $note ?? ('Xuất kho từ phiếu ' . $receipt->code),
        ]);
    }

    /**
     * Bán lẻ (từ hóa đơn vehicle_sales)
     */
    public static function sale(Vehicle $vehicle, VehicleSale $sale, ?string $note = null): InventoryLog
    {
        return self::write([
            'vehicle_id'        => $vehicle->id,
            'log_type'          => 'sale',
            'ref_table'         => 'vehicle_sales',
            'ref_id'            => $sale->id,
            'from_warehouse_id' => $vehicle->warehouse_id,
            'to_warehouse_id'   => null,
            'log_date'          => $sale->sale_date ? $sale->sale_date . ' 00:00:00' : now(),
            'note'              => $note ?? ('Bán lẻ HĐ ' . $sale->code),
        ]);
    }

    /**
     * Điều chỉnh tồn (từ phiếu DC_x)
     * $action: add / remove / other (theo inventory_adjustment_items.action)
     */
    public static function adjustment(Vehicle $vehicle, InventoryAdjustment $adj, string $action, ?string $note = null): InventoryLog
    {
        $actionText = match ($action) {
            'add'    => 'Điều chỉnh +1 (tăng tồn)',
            'remove' => 'Điều chỉnh -1 (giảm tồn)',
            default  => 'Điều chỉnh tồn kho',
        };

        return self::write([
            'vehicle_id'        => $vehicle->id,
            'log_type'          => 'adjustment',
            'ref_table'         => 'inventory_adjustments',
            'ref_id'            => $adj->id,
            'from_warehouse_id' => $adj->warehouse_id,
            'to_warehouse_id'   => null,
            'log_date'          => now(),
            'note'              => trim(($note ?: '') . ' ' . $actionText),
        ]);
    }
}
