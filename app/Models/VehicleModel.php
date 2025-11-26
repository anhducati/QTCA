<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    protected $table = 'vehicle_models';

    protected $fillable = [
        'brand_id',
        'code',
        'name',
        'vehicle_type',
        'cylinder_cc',
        'year_default',
        'note',
        'is_active',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'model_id');
    }
}
