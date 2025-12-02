<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportReceiptItem extends Model
{
    protected $fillable = [
        'export_receipt_id',
        'vehicle_id',
        'model_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'amount',
        'license_plate',
        'note',
    ];

    public function receipt()
    {
        return $this->belongsTo(ExportReceipt::class, 'export_receipt_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function exportReceipt()
    {
        return $this->belongsTo(ExportReceipt::class, 'export_receipt_id');
    }

    public function exportReceiptItems()
    {
        return $this->hasMany(\App\Models\ExportReceiptItem::class, 'vehicle_id');
    }


}
