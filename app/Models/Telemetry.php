<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telemetry extends Model
{
    use HasFactory;

    protected $table = 'telemetry';

    // has custom sampled_at timestamp
    public $timestamps = false;

    protected $fillable = [
        'olt_id',
        'onu_id',
        'metric',
        'value',
        'unit',
        'sampled_at',
    ];

    protected $casts = [
        'value' => 'decimal:3',
        'sampled_at' => 'datetime',
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
