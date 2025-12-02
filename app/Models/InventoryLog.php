<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $table = 'inventory_logs';

    protected $fillable = [
        'vehicle_id',
        'log_type',
        'ref_table',
        'ref_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'log_date',
        'note',
        'created_by',
    ];

    // Xe liên quan
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Kho xuất
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    // Kho nhập
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    // Người tạo log
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
