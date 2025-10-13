<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_type',
        'address',
        'document_number', 
        'emergency_contact',
        'monthly_cost',
        'extra_services',
        'installation_contact',
        'billing_contact',
        'service_start_date',
        'special_notes'
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'extra_services' => 'array',
        'service_start_date' => 'date'
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con las ONUs asignadas (la crearemos después)
     */
    public function assignedOnus()
    {
        // Por ahora retornamos una relación vacía
        return $this->hasMany(\App\Models\Customer::class)->where('id', 0);
    }

    /**
     * Scope para clientes activos
     */
    public function scopeActive($query)
    {
        return $query->whereHas('user', function($q) {
            $q->where('active', true);
        });
    }

    /**
     * Accesor para el nombre (accede a través de user)
     */
    public function getNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Accesor para el email (accede a través de user)
     */
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    /**
     * Accesor para el teléfono (accede a través de user)
     */
    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }
}