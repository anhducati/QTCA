<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = [
        'code',
        'adjustment_date',
        'warehouse_id',
        'reason',
        'stock_take_id',
        'note',
        'created_by',
        'approved_by',
    ];

    protected $dates = ['adjustment_date'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryAdjustmentItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
