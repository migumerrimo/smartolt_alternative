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
}
