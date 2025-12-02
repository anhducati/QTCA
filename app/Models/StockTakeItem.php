<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $table = 'stock_take_items';

    protected $fillable = [
        'stock_take_id',
        'vehicle_id',
        'frame_no',
        'engine_no',
        'system_exists',
        'is_present',
        'note',
    ];

    protected $casts = [
        'system_exists' => 'boolean',
        'is_present'    => 'boolean',
    ];

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
