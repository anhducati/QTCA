<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các trường được phép fill
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'image',
        'remember_token',
        'email_verified_at',
        'is_admin',
        'status',
    ];

    /**
     * Các trường ẩn khi serialize
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kiểu dữ liệu
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * ================================
     * QUAN HỆ: MODULE PERMISSIONS
     * ================================
     */
    public function modulePermissions()
    {
        return $this->hasMany(ModulePermission::class);
    }

    /**
     * ================================
     * KIỂM TRA QUYỀN MODULE + CRUD
     * ================================
     *
     * @param string $module  (vd: 'brands','models','warehouses'...)
     * @param string $action  (create|read|update|delete)
     *
     * @return bool
     */
    public function canModule(string $module, string $action): bool
    {
        // Nếu là Admin T1 → FULL quyền
        if ((int) $this->is_admin === 1) {
            return true;
        }

        // Lấy quyền đã lưu trong CSDL
        $perm = $this->modulePermissions
            ->firstWhere('module_key', $module);

        if (!$perm) {
            return false;
        }

        $column = 'can_' . $action;

        return (bool) ($perm->$column ?? false);
    }
}
