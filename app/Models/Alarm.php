<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alarm extends Model
{
    use HasFactory;

    protected $table = 'alarms';

    public $timestamps = false; // uses detected_at

    protected $fillable = [
        'olt_id',
        'onu_id',
        'severity',
        'message',
        'active',
        'detected_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'detected_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function onu()
    {
        return $this->belongsTo(Onu::class, 'onu_id');
    }
}
