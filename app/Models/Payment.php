<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'export_receipt_id',
        'payment_date',
        'amount',
        'method',
        'note',
    ];

    protected $dates = [
        'payment_date',
    ];

    public function receipt()
    {
        return $this->belongsTo(ExportReceipt::class, 'export_receipt_id');
    }
}
