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

    public function assignedOnus()
    {
        return $this->hasMany(CustomerOnuAssignment::class, 'customer_id');
    }

    /**
     * Relación con el perfil de cliente (si existe)
     */
    public function customerProfile()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Helper para verificar si es cliente
     */
    public function isCustomer()
    {
        return $this->role === 'customer' && $this->customerProfile !== null;
    }

    /**
     * Relación con las ONUs asignadas (a través del perfil de cliente)
     */
    public function customerAssignedOnus()
    {
        return $this->hasManyThrough(
            CustomerOnuAssignment::class,
            Customer::class,
            'user_id', // Foreign key on customers table
            'customer_id', // Foreign key on customer_onu_assignments table
            'id', // Local key on users table
            'id' // Local key on customers table
        );
    }


        /**
     * Relación con las asignaciones realizadas
     */
    public function onuAssignmentsMade()
    {
        return $this->hasMany(CustomerOnuAssignment::class, 'assigned_by');
    }
}