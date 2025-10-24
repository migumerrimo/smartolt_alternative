<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\OltTelnetService;
use App\Models\ChangeHistory;
use Exception;

class OltTelnetApiController extends Controller
{
    protected OltTelnetService $olt;

    public function __construct(OltTelnetService $olt)
    {
        $this->olt = $olt;
    }

    /**
     * 🔹 GET /api/olt/telnet/system/status
     * Devuelve versión, modelo y uptime de la OLT.
     */
    public function systemStatus()
    {
        try {
            $data = $this->olt->getSystemStatus();
            $this->logChange('OLT', 'display version', 'Consulta de estado del sistema', $data);
            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            Log::error("Telnet systemStatus error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 GET /api/olt/telnet/onus/list
     * Lista las ONUs registradas.
     */
    public function listOnus()
    {
        try {
            $data = $this->olt->listOnus();
            $this->logChange('OLT', 'display ont info', 'Consulta de lista de ONUs', $data);
            return response()->json(['success' => true, 'onus' => $data]);
        } catch (Exception $e) {
            Log::error("Telnet listOnus error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 POST /api/olt/telnet/onus/add
     * Registra una nueva ONU en la OLT.
     */
    public function addOnu(Request $request)
    {
        $validated = $request->validate([
            'slot' => 'required|integer',
            'port' => 'required|integer',
            'sn' => 'required|string',
            'lineProfile' => 'required|integer',
            'srvProfile' => 'required|integer',
            'desc' => 'nullable|string'
        ]);

        try {
            $res = $this->olt->addOnu(
                $validated['slot'],
                $validated['port'],
                $validated['sn'],
                $validated['lineProfile'],
                $validated['srvProfile'],
                $validated['desc'] ?? ''
            );

            $this->logChange('ONU', 'ont add', 'Registro de nueva ONU', $res);
            return response()->json(['success' => true, 'result' => $res]);
        } catch (Exception $e) {
            Log::error("Telnet addOnu error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 GET /api/olt/telnet/alarms/active
     * Devuelve alarmas activas clasificadas por criticidad.
     */
    public function getAlarms()
    {
        try {
            $alarms = $this->olt->getActiveAlarms();
            $this->logChange('OLT', 'display alarm active all', 'Consulta de alarmas activas', $alarms);
            return response()->json(['success' => true, 'alarms' => $alarms]);
        } catch (Exception $e) {
            Log::error("Telnet getAlarms error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 POST /api/olt/telnet/vlan/create
     * Crea una VLAN nueva.
     */
    public function createVlan(Request $request)
    {
        $validated = $request->validate([
            'vlan_id' => 'required|integer|min:1|max:4094',
            'description' => 'nullable|string|max:100'
        ]);

        try {
            $res = $this->olt->createVlan($validated['vlan_id'], $validated['description'] ?? '');
            $this->logChange('OLT', 'vlan create', "Creación de VLAN {$validated['vlan_id']}", $res);
            return response()->json(['success' => true, 'result' => $res]);
        } catch (Exception $e) {
            Log::error("Telnet createVlan error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 POST /api/olt/telnet/command
     * Ejecuta un comando libre Telnet (display o configuración).
     */
    public function runCommand(Request $request)
    {
        $validated = $request->validate([
            'command' => 'required|string',
            'config' => 'nullable|boolean'
        ]);

        try {
            $out = $this->olt->runCommand($validated['command'], $validated['config'] ?? false);
            $this->logChange('OLT', $validated['command'], 'Ejecución de comando libre', $out);
            return response()->json(['success' => true, 'output' => $out]);
        } catch (Exception $e) {
            Log::error("Telnet runCommand error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /* --------------------------------------------------------------------
     * 🔸 Método auxiliar: registrar historial de cambios
     * -------------------------------------------------------------------- */
    protected function logChange(string $deviceType, string $command, string $description, array|string $result = null): void
    {
        try {
            ChangeHistory::create([
                'user_id' => Auth::id() ?? 1,
                'olt_id' => 1,
                'device_type' => strtoupper($deviceType),
                'device_name' => $this->olt->host ?? 'OLT Huawei MA5680T',
                'command' => $command,
                'result' => is_array($result) ? json_encode($result, JSON_PRETTY_PRINT) : (string)$result,
                'description' => $description,
            ]);
        } catch (Exception $e) {
            Log::warning("No se pudo registrar el cambio: " . $e->getMessage());
        }
    }
}
