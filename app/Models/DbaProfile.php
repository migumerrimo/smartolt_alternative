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
        'type',
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
