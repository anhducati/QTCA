<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
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

    protected $dates = ['log_date'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
