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
    protected static function baseLog(
        Vehicle $vehicle,
        string $logType,
        ?string $refTable,
        ?int $refId,
        ?int $fromWarehouseId,
        ?int $toWarehouseId,
        ?string $note = null
    ): void {
        InventoryLog::create([
            'vehicle_id'        => $vehicle->id,
            'log_type'          => $logType,
            'ref_table'         => $refTable,
            'ref_id'            => $refId,
            'from_warehouse_id' => $fromWarehouseId,
            'to_warehouse_id'   => $toWarehouseId,
            'log_date'          => now(),
            'note'              => $note,
            'created_by'        => Auth::id() ?? 0,
        ]);
    }

    // NHẬP KHO
    public static function logImport(Vehicle $vehicle, ImportReceipt $receipt, ?string $note = null): void
    {
        self::baseLog(
            $vehicle,
            'import',
            'import_receipts',
            $receipt->id,
            null,
            $receipt->warehouse_id,
            $note ?? ('Nhập kho ' . $receipt->code)
        );
    }

    // XUẤT KHO / BÁN BUÔN
    public static function logExport(Vehicle $vehicle, ExportReceipt $receipt, ?string $note = null): void
    {
        self::baseLog(
            $vehicle,
            'export',
            'export_receipts',
            $receipt->id,
            $receipt->warehouse_id,
            null,
            $note ?? ('Xuất kho ' . $receipt->code)
        );
    }

    // BÁN LẺ
    public static function logRetailSale(Vehicle $vehicle, VehicleSale $sale, ?string $note = null): void
    {
       
    // TEST: xem có chạy vào đây không

       
        self::baseLog(
            $vehicle,
            'sale',
            'vehicle_sales',
            $sale->id,
            $vehicle->warehouse_id,
            null,
            $note ?? ('Bán lẻ HĐ ' . $sale->code)
        );
    }

    // ĐIỀU CHỈNH TỒN
    public static function logAdjustment(?Vehicle $vehicle, InventoryAdjustment $adj, ?string $action = null, ?string $note = null): void
    {
        if (!$vehicle) {
            return;
        }

        $warehouseId = $adj->warehouse_id ?? $vehicle->warehouse_id;

        self::baseLog(
            $vehicle,
            'adjustment',
            'inventory_adjustments',
            $adj->id,
            $warehouseId,
            $warehouseId,
            $note ?? ('Điều chỉnh tồn (action: ' . ($action ?: 'n/a') . ')')
        );
    }
}
