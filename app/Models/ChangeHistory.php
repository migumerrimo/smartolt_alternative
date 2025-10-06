<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeHistory extends Model
{
    use HasFactory;

    protected $table = 'change_history';

    public $timestamps = false; // uses date column

    protected $fillable = [
        'user_id',
        'olt_id',
        'device_type',
        'device_name',
        'command',
        'result',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function olt()
    {
        return $this->belongsTo(Olt::class, 'olt_id');
    }
}
