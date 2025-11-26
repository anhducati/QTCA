<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address',
        'tax_code',
        'note',
        'is_active',
    ];

    public function importReceipts()
    {
        return $this->hasMany(ImportReceipt::class);
    }
}
