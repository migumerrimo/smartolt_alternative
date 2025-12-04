<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProfile extends Model
{
    use HasFactory;

    protected $table = 'service_profiles';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'olt_id',
        'profile_id',
        'name',
        'service',
        'eth_ports',
        'binding_times',
        'vlan_id',
    ];

    protected $casts = [
        'eth_ports' => 'integer',
        'created_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function vlan()
    {
        return $this->belongsTo(Vlan::class, 'vlan_id');
    }

    public function onus()
    {
        return $this->hasMany(Onu::class, 'service_profile_id');
    }
}
