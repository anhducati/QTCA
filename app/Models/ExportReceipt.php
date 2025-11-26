<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportReceipt extends Model
{
    protected $fillable = [
        'code',
        'export_date',
        'warehouse_id',
        'customer_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(ExportReceiptItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
