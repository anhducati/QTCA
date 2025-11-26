<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportReceiptItem extends Model
{
    protected $fillable = [
        'import_receipt_id',
        'vehicle_id',
        'model_id',
        'quantity',
        'unit_price',
        'vat_percent',
        'amount',
        'note',
    ];

    public function receipt()
    {
        return $this->belongsTo(ImportReceipt::class, 'import_receipt_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }
}
