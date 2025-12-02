<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleSale extends Model
{
    use HasFactory;

    protected $table = 'vehicle_sales';

    protected $fillable = [
        'code',
        'sale_date',
        'vehicle_id',
        'customer_id',

        'sale_price',
        'discount',
        'amount',

        'paid_amount',
        'debt_amount',
        'payment_status', 
        'payment_method',

        'note',
        'created_by',
    ];

    protected $casts = [
        'sale_date'    => 'date',
        'sale_price'   => 'integer',
        'discount'     => 'integer',
        'amount'       => 'integer',
        'paid_amount'  => 'integer',
        'debt_amount'  => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | QUAN HỆ
    |--------------------------------------------------------------------------
    */

    // Xe đã bán
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Khách hàng mua xe
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // User tạo hoá đơn bán lẻ
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Lịch sử thanh toán (trả góp / trả thêm)
    public function payments()
    {
        return $this->hasMany(VehicleSalePayment::class, 'vehicle_sale_id');
    }

    // Alias cho đẹp: paymentsRetail()
    public function paymentsRetail()
    {
        return $this->hasMany(VehicleSalePayment::class, 'vehicle_sale_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS / LOGIC
    |--------------------------------------------------------------------------
    */

    // Tổng số lần thanh toán
    public function getPaymentCountAttribute()
    {
        return $this->payments()->count();
    }

    // Kiểm tra còn nợ không
    public function getIsDebtAttribute()
    {
        return $this->debt_amount > 0;
    }

    // Kiểm tra là trả góp không
    public function getIsInstallmentAttribute()
    {
        return $this->payment_method === 'installment';
    }
}
