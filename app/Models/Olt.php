<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Olt extends Model
{
    use HasFactory;

    protected $table = 'olts';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'model',
        'vendor',
        'management_ip',
        'location',
        'firmware',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relations
    public function vlans()
    {
        return $this->hasMany(Vlan::class, 'olt_id');
    }

    public function dbaProfiles()
    {
        return $this->hasMany(DbaProfile::class, 'olt_id');
    }

    public function lineProfiles()
    {
        return $this->hasMany(LineProfile::class, 'olt_id');
    }

    public function serviceProfiles()
    {
        return $this->hasMany(ServiceProfile::class, 'olt_id');
    }

    public function onus()
    {
        return $this->hasMany(Onu::class, 'olt_id');
    }

    public function trafficTables()
    {
        return $this->hasMany(TrafficTable::class, 'olt_id');
    }

    public function servicePorts()
    {
        return $this->hasMany(ServicePort::class, 'olt_id');
    }

    public function telemetries()
    {
        return $this->hasMany(Telemetry::class, 'olt_id');
    }

    public function alarms()
    {
        return $this->hasMany(Alarm::class, 'olt_id');
    }

    public function changeHistories()
    {
        return $this->hasMany(ChangeHistory::class, 'olt_id');
    }

    public function deviceConfigs()
    {
        return $this->hasMany(DeviceConfig::class, 'olt_id');
    }
}
