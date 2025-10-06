<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficTable extends Model
{
    use HasFactory;

    protected $table = 'traffic_table';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'olt_id',
        'name',
        'cir',
        'pir',
        'priority',
    ];

    protected $casts = [
        'cir' => 'integer',
        'pir' => 'integer',
        'priority' => 'integer',
        'created_at' => 'datetime',
    ];

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }

    public function servicePorts()
    {
        return $this->hasMany(ServicePort::class, 'traffic_table_id');
    }
}
