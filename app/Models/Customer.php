<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address',
        'cccd',
        'dob',
        'gender',
        'note',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function exportReceipts()
    {
        return $this->hasMany(ExportReceipt::class);
    }
    public function vehicleSales()
    {
        return $this->hasMany(\App\Models\VehicleSale::class, 'customer_id');
    }

}
