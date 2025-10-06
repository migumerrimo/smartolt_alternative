<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relations
    public function changeHistory()
    {
        return $this->hasMany(ChangeHistory::class, 'user_id');
    }

    public function appliedDeviceConfigs()
    {
        return $this->hasMany(DeviceConfig::class, 'applied_by');
    }
}
