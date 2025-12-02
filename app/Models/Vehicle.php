<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand; // 

class Vehicle extends Model
{
    // Nếu tên bảng đúng "vehicles" thì không cần $table.

    protected $fillable = [
        'model_id',
        'color_id',
        'warehouse_id',
        'supplier_id',
        'import_receipt_id',

        'frame_no',
        'engine_no',
        'year',
        'battery_no',
        'imei',

        'license_plate',
        'registered_at',          // ngày đăng ký biển
        'status',                 // in_stock / sold / reserved / ...

        'purchase_price',         // giá nhập 1 xe
        'import_price',           // nếu anh dùng tổng giá trên phiếu hoặc giá NCC
        'sale_price',
        'sale_date',

        'customer_id',
        'note',

        'supplier_paid',
        'supplier_paid_at',
        'registration_received',
        'registration_received_at',
        'brand_id',

    ];

    protected $casts = [
        'year'                    => 'integer',

        'purchase_price'          => 'decimal:2',
        'import_price'            => 'decimal:2',
        'sale_price'              => 'decimal:2',

        'sale_date'               => 'date',
        'registered_at'           => 'date',
        'supplier_paid_at'        => 'date',
        'registration_received_at'=> 'date',

        'supplier_paid'           => 'boolean',   // 0/1 -> false/true
        'registration_received'   => 'boolean',   // 0/1 -> false/true
    ];

    /*
    |--------------------------------------------------------------------------
    | QUAN HỆ
    |--------------------------------------------------------------------------
    */

        public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function importReceipt()
    {
        return $this->belongsTo(ImportReceipt::class, 'import_receipt_id');
    }

    public function importItems()
    {
        // nếu bảng chi tiết phiếu nhập tên là import_receipt_items và có vehicle_id
        return $this->hasMany(ImportReceiptItem::class, 'vehicle_id');
    }

    public function exportItems()
    {
        // nếu bảng chi tiết phiếu xuất tên là export_receipt_items và có vehicle_id
        return $this->hasMany(ExportReceiptItem::class, 'vehicle_id');
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class, 'vehicle_id');
    }

        public function exportReceiptItems()
    {
        return $this->hasMany(\App\Models\ExportReceiptItem::class, 'vehicle_id');
    }

        public function retailSale()
    {
        return $this->hasOne(\App\Models\VehicleSale::class, 'vehicle_id');
    }


}
