<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vlan extends Model
{
    use HasFactory;

    protected $table = 'vlans';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'olt_id',
        'number',
        'type',
        'description',
        'uplink_port',
        'port_mode',
        'native_port',
        'native_vlan',
        'vlanif_ip',
        'vlanif_netmask',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'number' => 'integer',
    ];

    // Relations
    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function lineProfiles()
    {
        return $this->hasMany(LineProfile::class, 'vlan_id');
    }

    public function serviceProfiles()
    {
        return $this->hasMany(ServiceProfile::class, 'vlan_id');
    }

    public function servicePorts()
    {
        return $this->hasMany(ServicePort::class, 'vlan_id');
    }
}
