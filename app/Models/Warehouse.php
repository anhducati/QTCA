<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'code', 'name', 'address', 'note', 'is_active',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function importReceipts()
    {
        return $this->hasMany(ImportReceipt::class);
    }

    public function exportReceipts()
    {
        return $this->hasMany(ExportReceipt::class);
    }

    public function stockTakes()
    {
        return $this->hasMany(StockTake::class);
    }

    public function inventoryAdjustments()
    {
        return $this->hasMany(InventoryAdjustment::class);
    }
}
