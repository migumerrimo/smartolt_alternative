<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePort extends Model
{
    use HasFactory;

    protected $table = 'service_ports';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'olt_id',
        'onu_id',
        'vlan_id',
        'traffic_table_id',
        'gemport_id',
        'type',
    ];

    protected $casts = [
        'gemport_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function onu()
    {
        return $this->belongsTo(Onu::class, 'onu_id');
    }

    public function vlan()
    {
        return $this->belongsTo(Vlan::class, 'vlan_id');
    }

    public function trafficTable()
    {
        return $this->belongsTo(TrafficTable::class, 'traffic_table_id');
    }
}
