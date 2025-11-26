<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'model_id',
        'color_id',
        'warehouse_id',
        'frame_no',
        'engine_no',
        'year',
        'battery_no',
        'imei',
        'license_plate',
        'registered_at',
        'status',
        'import_price',
        'sale_price',
        'sale_date',
        'customer_id',
        'note',
    ];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function importItems()
    {
        return $this->hasMany(ImportReceiptItem::class);
    }

    public function exportItems()
    {
        return $this->hasMany(ExportReceiptItem::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }
}
