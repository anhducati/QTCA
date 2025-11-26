<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $fillable = [
        'stock_take_id',
        'vehicle_id',
        'frame_no',
        'engine_no',
        'system_exists',
        'is_present',
        'note',
    ];

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class, 'stock_take_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
