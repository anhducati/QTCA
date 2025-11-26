<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustmentItem extends Model
{
    protected $fillable = [
        'inventory_adjustment_id',
        'vehicle_id',
        'frame_no',
        'engine_no',
        'action',
        'qty',
        'note',
    ];

    public function adjustment()
    {
        return $this->belongsTo(InventoryAdjustment::class, 'inventory_adjustment_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
