<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportReceipt extends Model
{
    protected $fillable = [
        'code',
        'export_date',
        'warehouse_id',
        'customer_id',      // thực chất là supplier_id
        'export_type',
        'total_amount',
        'paid_amount',
        'debt_amount',
        'payment_status',
        'due_date',
        'note',
        'created_by',
        'approved_by',
    ];

    protected $dates = [
        'export_date',
        'due_date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // "Khách hàng" = Nhà cung cấp
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(ExportReceiptItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
