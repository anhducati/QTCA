<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleSalePayment extends Model
{
    protected $table = 'vehicle_sale_payments';

    protected $fillable = [
        'vehicle_sale_id',
        'payment_date',
        'amount',
        'method',
        'note',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'integer',
    ];

    public function sale()
    {
        return $this->belongsTo(VehicleSale::class, 'vehicle_sale_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
