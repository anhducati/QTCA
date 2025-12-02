<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $table = 'inventory_adjustments';

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

    // Kho
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Phiếu kiểm kê liên quan (nếu có)
    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    // Các dòng điều chỉnh
    public function items()
    {
        return $this->hasMany(InventoryAdjustmentItem::class, 'inventory_adjustment_id');
    }

    // Người tạo phiếu
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Người duyệt phiếu (nếu anh có dùng sau này)
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
