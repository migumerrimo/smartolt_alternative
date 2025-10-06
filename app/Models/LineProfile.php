<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineProfile extends Model
{
    use HasFactory;

    protected $table = 'line_profiles';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'olt_id',
        'name',
        'dba_profile_id',
        'tcont',
        'gem_ports',
        'vlan_id',
    ];

    protected $casts = [
        'tcont' => 'integer',
        'gem_ports' => 'integer',
        'created_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function dbaProfile()
    {
        return $this->belongsTo(DbaProfile::class, 'dba_profile_id');
    }

    public function vlan()
    {
        return $this->belongsTo(Vlan::class, 'vlan_id');
    }

    public function onus()
    {
        return $this->hasMany(Onu::class, 'line_profile_id');
    }
}
