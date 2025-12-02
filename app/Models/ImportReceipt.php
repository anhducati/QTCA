<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ImportReceipt extends Model
{
    protected $fillable = [
        'code',
        'import_date',
        'supplier_id',
        'warehouse_id',
        'total_amount',
        'note',
        'created_by',
        'approved_by',
    ];

    protected $dates = [
        'import_date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(ImportReceiptItem::class);
    }

    // ⚠ Quan hệ cũ anh đang có
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ✅ Thêm alias createdBy để phù hợp với controller + view
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
        // hoặc return $this->creator();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'import_receipt_id');
    }
}
