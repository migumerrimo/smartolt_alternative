<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Services\OltSshService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\DbaProfile;

class OltSshDbaProfileController extends Controller
{
    /**
     * Obtener listado de DBA Profiles desde la OLT
     */
    public function listFromOlt($oltId)
    {
        $olt = Olt::findOrFail($oltId);

        try {
            $ssh = new OltSshService(
                $olt->management_ip,
                $olt->ssh_port,
                $olt->ssh_username,
                $olt->ssh_password
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'No se pudo conectar a la OLT: ' . $e->getMessage()
            ], 500);
        }

        $resp = $ssh->getDbaProfiles();
        $output = $resp['raw'] ?? '';

        Log::info('OLT raw output for DBA Profile list', [
            'olt_id' => $oltId,
            'management_ip' => $olt->management_ip,
            'raw' => $output
        ]);

        $profiles = [];

        // Parseamos la salida de la OLT
        // Buscamos tabla con encabezados: Profile-ID, type, Bandwidth compensation, Fix, Assure, Max, Bind times
        $lines = preg_split('/\r?\n/', $output);
        $headerIndex = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/\bProfile-ID\b.*\btype\b/i', $line)) {
                $headerIndex = $i;
                break;
            }
        }

        if ($headerIndex !== null) {
            // Buscar la línea separadora (----) después del header
            $dataStart = null;
            for ($j = $headerIndex + 1; $j < count($lines); $j++) {
                if (preg_match('/^-{3,}/', trim($lines[$j]))) {
                    $dataStart = $j + 1;
                    break;
                }
            }

            if ($dataStart !== null) {
                // Recoger filas hasta encontrar una línea vacía
                for ($k = $dataStart; $k < count($lines); $k++) {
                    $row = trim($lines[$k]);
                    if ($row === '' || preg_match('/^-{3,}/', $row)) {
                        break;
                    }

                    // Parseo flexible: Profile-ID (número) y el resto de valores
                    if (preg_match('/^\s*(\d+)\s+(\d+)\s+(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $row, $m)) {
                        $profiles[] = [
                            'profile_id' => intval($m[1]),
                            'type' => intval($m[2]),
                            'bandwidth_compensation' => $m[3],
                            'fix_kbps' => intval($m[4]),
                            'assure_kbps' => intval($m[5]),
                            'max_kbps' => intval($m[6]),
                            'bind_times' => intval($m[7])
                        ];
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $profiles,
            'raw'     => $output
        ]);
    }

    /**
     * Crear DBA Profile en la OLT
     */
    public function createOnOlt(Request $request, $oltId)
    {
        try {
            $request->validate([
                'profile_id' => 'required|integer',
                'profile_name' => 'required|string',
                'type' => 'required|in:1,2,3,4',
                'max_kbps' => 'required|integer|min:0',
            ]);

            $olt = Olt::findOrFail($oltId);

            try {
                $ssh = new OltSshService(
                    $olt->management_ip,
                    $olt->ssh_port,
                    $olt->ssh_username,
                    $olt->ssh_password
                );
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error'   => 'No se pudo conectar a la OLT: ' . $e->getMessage()
                ], 500);
            }

            $profileId = $request->profile_id;
            $profileName = $request->profile_name;
            $type = $request->type;
            $maxKbps = $request->max_kbps;

            // Construir comando según la imagen
            $commands = [];
            $commands[] = 'enable';
            $commands[] = 'config';
            $commands[] = "dba-profile add profile-id {$profileId} profile-name \"{$profileName}\" type{$type} max {$maxKbps}";
            $commands[] = 'quit';

            $out = $ssh->exec($commands);
            $raw = $out['raw'] ?? '';

            // Determinar éxito
            $isError = false;
            if (preg_match('/(Error:|%\s+Unknown|Unknown command|Error\s|failed)/i', $raw)) {
                $isError = true;
            }

            $success = !$isError && preg_match('/succeeded/i', $raw);

            // Registrar en historial
            try {
                $cmds = implode(' | ', $commands);
                \App\Helpers\ChangeLogger::log('OLT', $olt->name, "Creación de DBA Profile {$profileId}", $cmds, substr($raw, 0, 2000), $olt->id);
            } catch (\Exception $e) {
                Log::error('ChangeLogger failed', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => $success,
                'message' => $success ? "DBA Profile {$profileId} creado en la OLT" : 'Error ejecutando comandos en la OLT',
                'commands' => $commands,
                'olt_output' => $raw
            ], $success ? 200 : 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating DBA Profile on OLT', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
