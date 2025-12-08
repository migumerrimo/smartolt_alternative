<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbaProfile extends Model
{
    use HasFactory;

    protected $table = 'dba_profiles';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'olt_id',
        'name',
        'profile_id',
        'type',
        'bandwidth_compensation',
        'fix_kbps',
        'assure_kbps',
        'max_kbps',
        'bind_times',
        'max_bandwidth',
    ];

    protected $casts = [
        'max_bandwidth' => 'integer',
        'created_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function lineProfiles()
    {
        return $this->hasMany(LineProfile::class, 'dba_profile_id');
    }
}
