<?php

namespace App\Helpers;

use App\Models\ChangeHistory;
use Illuminate\Support\Facades\Auth;

class ChangeLogger
{
    public static function log($deviceType, $deviceName, $description, $command = null, $result = null, $olt_id = null)
    {
        ChangeHistory::create([
            'user_id'     => Auth::id() ?? 1,
            'olt_id'      => $olt_id,
            'device_type' => strtoupper($deviceType),
            'device_name' => $deviceName,
            'command'     => $command,
            'result'      => $result,
            'description' => $description,
        ]);
    }
}
