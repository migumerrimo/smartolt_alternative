<?php

namespace App\Helpers;

use App\Models\ChangeHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChangeLogger
{
    public static function log($deviceType, $deviceName, $description, $command = null, $result = null, $olt_id = null)
    {
        $upper = strtoupper($deviceType);
        // device_type column is an enum limited to OLT, ONU, ROUTER, SWITCH, SERVER
        $allowed = ['OLT','ONU','ROUTER','SWITCH','SERVER'];
        $deviceTypeSafe = in_array($upper, $allowed) ? $upper : 'SERVER';

        // Guardar en DB si es posible
        try {
            ChangeHistory::create([
                'user_id'     => Auth::id() ?? 1,
                'olt_id'      => $olt_id,
                'device_type' => $deviceTypeSafe,
                'entity_type' => $upper,
                'device_name' => $deviceName,
                'command'     => $command,
                'result'      => $result,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            // Si falla (por ejemplo sin DB), continuamos y guardamos en fichero
        }

        // Siempre escribir también a fichero JSONL para fallback/visualización sin DB
        try {
            $entry = [
                'user_id' => Auth::id() ?? 1,
                'olt_id' => $olt_id,
                'device_type' => $deviceTypeSafe,
                'entity_type' => $upper,
                'device_name' => $deviceName,
                'command' => $command,
                'result' => $result,
                'description' => $description,
                'date' => Carbon::now()->toDateTimeString()
            ];

            $path = storage_path('logs/change_history.log');
            $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n";
            // Aseguramos que el directorio exista y luego append
            file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // No hacemos nada si falló el logging a fichero
        }
    }
}
