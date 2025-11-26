<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
