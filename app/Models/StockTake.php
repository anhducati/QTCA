<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTake extends Model
{
    protected $table = 'stock_takes';

    protected $fillable = [
        'code',
        'warehouse_id',
        'stock_take_date',
        'status',
        'note',
        'created_by',
        'approved_by',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_CONFIRMED = 'confirmed';

    // Kho
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Người tạo
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Người duyệt
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Chi tiết kiểm kê
    public function items()
    {
        return $this->hasMany(StockTakeItem::class);
    }

    // Phiếu điều chỉnh (nếu có)
    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class, 'stock_take_id');
    }
}
