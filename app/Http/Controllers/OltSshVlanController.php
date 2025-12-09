<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Services\OltSshService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Vlan;
use App\Helpers\ChangeLogger;

class OltSshVlanController extends Controller
{

    /**
     * Obtener listado de VLANs desde la OLT
     */
    public function listFromOlt($oltId)
    {
        $olt = Olt::findOrFail($oltId);

        // Instanciar el servicio por petición (el servicio requiere credenciales)
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

        $resp = $ssh->getVlans();
        $output = $resp['raw'] ?? '';

        // Log temporal para depuración: guarda la salida cruda de la OLT
        Log::info('OLT raw output for VLAN list', [
            'olt_id' => $oltId,
            'management_ip' => $olt->management_ip,
            'raw' => $output
        ]);

        $vlans = [];

        // Intentar detectar tabla con encabezados (MA5680T muestra columnas)
        $lines = preg_split('/\r?\n/', $output);
        $headerIndex = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/\bVLAN\b\s+\bType\b/i', $line)) {
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
                // Recoger filas hasta encontrar 'Total:' o una línea vacía
                for ($k = $dataStart; $k < count($lines); $k++) {
                    $row = trim($lines[$k]);
                    if ($row === '' || preg_match('/^Total:/i', $row) || preg_match('/^Note\s*:/i', $row)) {
                        break;
                    }

                    // Intentar parsear columnas fijas: VLAN Type Attribute STND-Port NUM SERV-Port NUM VLAN-Con NUM
                    if (preg_match('/^\s*(\d+)\s+(\S+)\s+(\S+)\s+(\d+)\s+(\d+)\s+(\S+)/', $row, $m)) {
                        $vlans[] = [
                            'number' => intval($m[1]),
                            'type' => $m[2],
                            'attribute' => $m[3],
                            'stnd_port_num' => intval($m[4]),
                            'serv_port_num' => intval($m[5]),
                            'vlan_con' => $m[6]
                        ];
                        continue;
                    }

                    // Fallback: parseo flexible (número + resto)
                    if (preg_match('/^\s*(\d+)\s+(\S.*)$/', $row, $m2)) {
                        $vlans[] = [
                            'number' => intval($m2[1]),
                            'name' => trim($m2[2])
                        ];
                    }
                }
            }
        } else {
            // Si no hay tabla, intentar capturar líneas que comiencen con número
            preg_match_all('/^\s*(\d+)\s+(\S.*)$/m', $output, $matches, PREG_SET_ORDER);
            foreach ($matches as $m) {
                $vlans[] = [
                    'number' => intval($m[1]),
                    'name' => trim($m[2])
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $vlans,
            'raw'     => $output
        ]);
    }

    /**
     * Crear VLAN en la OLT
     */
    public function createOnOlt(Request $request, $oltId)
    {
        $request->validate([
            'number' => 'required|integer',
            'type'   => 'required|string',
            'ports'  => 'nullable|string', // e.g. "0/7 0/8"
            'port_mode' => 'nullable|string',
            'native_vlan' => 'nullable|integer',
            'native_port' => 'nullable|string',
            'vlanif_ip' => 'nullable|ip',
            'vlanif_netmask' => 'nullable|string'
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

        $vlan = $request->number;
        $type = $request->type;

        // Build command sequence based on the MA5680T interactive steps
        $commands = [];
        // enter privileged and config mode
        $commands[] = 'enable';
        $commands[] = 'config';

        // create vlan
        $commands[] = "vlan {$vlan} {$type}";

        // assign ports to vlan if provided
        // Formato correcto Huawei: port vlan VLAN_ID SLOT/PORT PORT_LIST
        if ($request->filled('ports')) {
            // ports puede ser "0/7" o "0/7 0/8" - cada uno es un slot/port diferente
            $ports = preg_split('/[\s,]+/', trim($request->ports));
            $portMode = $request->input('port_mode', '1'); // default tag mode 1
            
            foreach ($ports as $slotPort) {
                if ($slotPort === '') continue;
                // slotPort es algo como "0/7"
                // El tercer parámetro es la lista de puertos en ese slot (ej: 1 o 0,1,4-5)
                // Por defecto usamos "1" (puerto 1)
                $portList = $portMode ?: '1';
                $commands[] = "port vlan {$vlan} {$slotPort} {$portList}";
            }
        }

        // native vlan on a specific port (interface scu)
        // IMPORTANTE: Solo si el puerto ya está en la VLAN
        if ($request->filled('native_port') && $request->filled('native_vlan')) {
            $nativePort = $request->native_port;
            $nativeVlan = $request->native_vlan;
            $commands[] = "interface scu {$nativePort}";
            $commands[] = "native-vlan {$nativeVlan} vlan {$vlan}";
            $commands[] = 'quit';
        }

        // optional: configure VLAN interface IP
        if ($request->filled('vlanif_ip')) {
            $net = $request->input('vlanif_netmask', '255.255.255.0');
            $commands[] = "interface vlanif {$vlan}";
            $commands[] = "ip address {$request->vlanif_ip} {$net}";
            $commands[] = 'quit';
        }

        // leave config and exit
        $commands[] = 'quit';

        $out = $ssh->exec($commands);
        $raw = $out['raw'] ?? '';

        // Determine success: if raw output contains common error markers, treat as failure
        $isError = false;
        if (preg_match('/(Error:|Failure:|%\s+Unknown|Unknown command|Error\s|conflicts)/i', $raw)) {
            $isError = true;
        }

        if (!$isError) {
            // Persist VLAN in DB (upsert) using the same params used to create on OLT
            try {
                $vlanModel = Vlan::updateOrCreate(
                    ['olt_id' => $olt->id, 'number' => $vlan],
                    [
                        'type' => $type,
                        'description' => $request->input('description'),
                        'uplink_port' => $request->input('ports'),
                        'port_mode' => $request->input('port_mode'),
                        'native_port' => $request->input('native_port'),
                        'native_vlan' => $request->input('native_vlan'),
                        'vlanif_ip' => $request->input('vlanif_ip'),
                        'vlanif_netmask' => $request->input('vlanif_netmask')
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Failed to persist VLAN record', ['error' => $e->getMessage()]);
            }
        }

        // Registrar en historial de cambios
        try {
            $cmds = implode(' | ', $commands);
            ChangeLogger::log('OLT', $olt->name, "Creación de VLAN {$vlan}", $cmds, substr($raw,0,2000), $olt->id);
        } catch (\Exception $e) {
            Log::error('ChangeLogger failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => !$isError,
            'message' => $isError ? 'Error ejecutando comandos en la OLT' : "VLAN {$vlan} creada en la OLT",
            'commands' => $commands,
            'olt_output' => $raw
        ], $isError ? 500 : 200);
    }
}
