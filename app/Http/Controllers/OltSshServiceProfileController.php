<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Services\OltSshService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\ChangeLogger;

class OltSshServiceProfileController extends Controller
{
    /**
     * Listar Service Profiles desde la OLT
     * GET /api/olt/ssh/service-profile/{oltId}/list
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

        $resp = $ssh->getServiceProfiles();
        $output = $resp['raw'] ?? '';

        Log::info('OLT service profiles raw output', [
            'olt_id' => $oltId,
            'management_ip' => $olt->management_ip,
            'raw' => $output
        ]);

        $profiles = [];

        // Parsear tabla con encabezados (Profile-ID | Profile-name | Binding times)
        $lines = preg_split('/\r?\n/', $output);
        $headerIndex = null;
        
        foreach ($lines as $i => $line) {
            if (preg_match('/\bProfile-ID\b.*\bProfile-name\b/i', $line)) {
                $headerIndex = $i;
                break;
            }
        }

        if ($headerIndex !== null) {
            // Buscar línea separadora (dashes)
            $dataStart = null;
            for ($j = $headerIndex + 1; $j < count($lines); $j++) {
                if (preg_match('/^-{3,}/', trim($lines[$j]))) {
                    $dataStart = $j + 1;
                    break;
                }
            }

            if ($dataStart !== null) {
                // Parsear filas de datos
                for ($k = $dataStart; $k < count($lines); $k++) {
                    $row = trim($lines[$k]);
                    if ($row === '' || preg_match('/^Total:/i', $row)) {
                        break;
                    }

                    // Patron: "0  srv-profile_default_0  0"
                    // Ajustado para permitir nombres con espacios. Captura:
                    // 1) profile id al inicio, 2) nombre (cualquier texto) y 3) binding times al final
                    if (preg_match('/^\s*(\d+)\s+(.+?)\s+(\d+)\s*$/', $row, $m)) {
                        $profiles[] = [
                            'profile_id' => intval($m[1]),
                            'name' => trim($m[2]),
                            'binding_times' => intval($m[3])
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
     * Crear Service Profile en la OLT (no persiste en DB)
     * POST /api/olt/ssh/service-profile/{oltId}/create
     */
    public function createOnOlt(Request $request, $oltId)
    {
        $request->validate([
            'profile_id' => 'required|integer',
            'name' => 'required|string',
            'ont_port_command' => 'nullable|string'
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

        $profileId = $request->input('profile_id');
        $name = $request->input('name');
        $ontPortCmd = $request->input('ont_port_command');

        $commands = [];
        $commands[] = 'enable';
        $commands[] = 'config';
        // create profile
        // wrap name in quotes to allow spaces
        $commands[] = "ont-srvprofile gpon profile-id {$profileId} profile-name \"{$name}\"";

        if ($ontPortCmd) {
            $commands[] = $ontPortCmd; // e.g. 'ont-port eth adaptive'
        }

        // commit and quit from profile
        $commands[] = 'commit';
        $commands[] = 'quit';

        $out = $ssh->exec($commands);
        $raw = $out['raw'] ?? '';

        $isError = false;
        if (preg_match('/(Error:|%\s+Unknown|Unknown command|conflicts|failed)/i', $raw)) {
            $isError = true;
        }

        // Registrar en historial de cambios
        try {
            ChangeLogger::log('OLT', $olt->name, "Creación de service-profile {$name} (id {$profileId})", implode(' | ', $commands), substr($raw, 0, 2000), $olt->id);
        } catch (\Exception $e) {
            Log::error('ChangeLogger failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => !$isError,
            'commands' => $commands,
            'olt_output' => $raw
        ], $isError ? 500 : 200);
    }
}
