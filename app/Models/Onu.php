<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onu extends Model
{
    use HasFactory;

    protected $table = 'onus';

    // has custom timestamps (registered_at / last_contact), so disable automatic timestamps
    public $timestamps = false;

    protected $fillable = [
        'olt_id',
        'serial_number',
        'model',
        'pon_port',
        'line_profile_id',
        'service_profile_id',
        'status',
        'last_contact',
        'registered_at',
    ];

    protected $casts = [
        'last_contact' => 'datetime',
        'registered_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function lineProfile()
    {
        return $this->belongsTo(LineProfile::class, 'line_profile_id');
    }

    public function serviceProfile()
    {
        return $this->belongsTo(ServiceProfile::class, 'service_profile_id');
    }

    public function servicePorts()
    {
        return $this->hasMany(ServicePort::class, 'onu_id');
    }

    public function telemetries()
    {
        return $this->hasMany(Telemetry::class, 'onu_id');
    }

    public function alarms()
    {
        return $this->hasMany(Alarm::class, 'onu_id');
    }

    public function customerAssignments()
    {
        return $this->hasMany(CustomerOnuAssignment::class, 'onu_id');
    }


}
