<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'code', 'name', 'note', 'is_active',
    ];

    public function models()
    {
        return $this->hasMany(VehicleModel::class);
    }
}
