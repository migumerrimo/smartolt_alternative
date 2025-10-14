<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOnuAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'onu_id',
        'assigned_by',
        'monthly_cost',
        'status',
        'notes'
    ];

    protected $attributes = [
        'status' => 'active'
    ];

        /**
     * Relación con el cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relación con la ONU
     */
    public function onu()
    {
        return $this->belongsTo(Onu::class);
    }

    /**
     * Relación con el usuario que asignó
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope para asignaciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}