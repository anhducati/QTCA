<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'code', 'name', 'hex_code', 'note', 'is_active',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
