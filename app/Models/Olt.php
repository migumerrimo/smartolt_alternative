<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Olt extends Model
{
    protected $table = 'olts';

    protected $fillable = [
        'name', 'model', 'vendor', 'management_ip', 'location', 'firmware',
        'ssh_username', 'ssh_password', 'ssh_port', 'ssh_active',
        'connector_type',
        'last_connection_at', 'last_connection_status', 'last_error',
        'connection_timeout', 'command_timeout',
        'auto_monitoring', 'status'
    ];

    public $timestamps = true;

    /**
     * ONUs connected to this OLT
     */
    public function onus()
    {
        return $this->hasMany(Onu::class, 'olt_id');
    }

    /**
     * Alarms related to this OLT
     */
    public function alarms()
    {
        return $this->hasMany(Alarm::class, 'olt_id');
    }

    /**
     * Telemetry measurements related to this OLT
     */
    public function telemetries()
    {
        return $this->hasMany(Telemetry::class, 'olt_id');
    }
}
